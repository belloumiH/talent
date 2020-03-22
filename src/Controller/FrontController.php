<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Offer;
use App\Entity\OfferSkill;
use App\v1\View\OfferView;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class FrontController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, string $languageUser)
    {
        $pagingIndex = $this->getParameter('paging_index');
        $orderBy = 1;
        $address = [];
        $skillsChoice = [];
        $addressChoice = '';
        try {
            if ($request->isMethod('post')) {
                $data = $request->request->all();
                if (true === isset($data['address-filter'])) {
                    $addressChoice = $data['address-filter'];
                }
                if (2 === (int) $data['time-filter']) {
                    $orderBy = 2;
                }
                if (true === isset($data['skill-filter']) and 0 < count($data['skill-filter'])) {
                    $skillsChoice = $data['skill-filter'];
                }

                $offers = $this->getDoctrine()
                    ->getRepository(Offer::class)
                    ->getByFilter($data);
//                $offers = $paginator->paginate(
//                    $query,
//                    $request->query->getInt('page', 1),
//                    $pagingIndex
//                );
            } else {
                $offers = $this->getDoctrine()
                    ->getRepository(Offer::class)
                    ->findBy(['enabled' => 1], ['createdAt' => 'DESC']);
            }
            $address = $this->getDoctrine()
                ->getRepository(Offer::class)
                ->getDistinctAddress();
            $skills = $this->getDoctrine()
                ->getRepository(OfferSkill::class)
                ->getDistinctSkills();
        } catch (Exception $e) {
            $offers = [];
            $address = [];
            $skills = [];
        }

        $translations = Yaml::parseFile(
            $this->getParameter('kernel.project_dir').'/translations/translation_'.$languageUser.'.yaml'
        );

        $numberPage = (int) ceil(count($offers) / $pagingIndex);
        $offers = array_slice($offers, 0, $pagingIndex);

        return $this->render('front/index.twig', [
            'languageUser' => $languageUser,
            'translations' => $translations,
            'offers' => $offers,
            'orderBy' => $orderBy,
            'address' => $address,
            'skills' => $skills,
            'addressChoice' => $addressChoice,
            'skillsChoice' => $skillsChoice,
            'numberPage' => $numberPage,
        ]);
    }

    public function offer(Request $request, string $languageUser, int $offerId)
    {
        $offers = $this->getDoctrine()
            ->getRepository(Offer::class)
            ->find($offerId);

        $projectRoot = $this->getParameter('kernel.project_dir');
        $translations = Yaml::parseFile($projectRoot.'/translations/translation_'.$languageUser.'.yaml');

        return $this->render('front/offer.twig', [
            'offers' => $offers,
            'languageUser' => $languageUser,
            'translations' => $translations,
        ]);
    }

    public function candidateInsert(Request $request, string $languageUser)
    {
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            $candidateFile = $request->files->get('candidate-file');
            $fileNameAws = null;
            if (null !== $candidateFile) {
                $fileName = microtime(true).'.'.$candidateFile->guessExtension();
                try {
                    $candidateFile->move(
                        $this->getParameter('uploads_private_directory'),
                        $fileName
                    );
                    $fileNameAws = $this->get('aws_storage')->uploadFile(
                        $this->getParameter('uploads_private_directory').'/'.$fileName,
                        'recrutalent/'
                    );
                    unlink($this->getParameter('uploads_private_directory').'/'.$fileName);
                } catch (Exception $e) {
                    $fileNameAws = null;
                }
            }

            $candidate = new Candidate();
            $candidate->setFirstName((string) $data['your-first-name']);
            $candidate->setLastName((string) $data['your-name']);
            $candidate->setPhone((string) $data['phone']);
            $candidate->setMail((string) $data['your-email']);
            $candidate->setComment((string) $data['message']);
            $candidate->setFile((string) $fileNameAws);
            $offer = $this->getDoctrine()
                ->getRepository(Offer::class)
                ->find((int) $data['offer-id']);
            $candidate->setOffer($offer);
            $this->insert($candidate);
        }

        return $this->redirectToRoute('index', ['languageUser' => $languageUser]);
    }

    public function error(Request $request)
    {
        return $this->render('front/error.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    public function insert($object)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($object);
        $entityManager->flush();

        return $object;
    }

    public function getAjaxOffer(Request $request)
    {
        $pagingIndex = $this->getParameter('paging_index');
        $orderBy = 1;
        try {
            if ($request->isMethod('post')) {
                $data = $request->request->all();
                if (2 === (int) $data['time-filter']) {
                    $orderBy = 2;
                }
                $data['time-filter'] = $orderBy;
                $offers = $this->getDoctrine()
                    ->getRepository(Offer::class)
                    ->getByFilter($data);
                $offset = (1 === (int) $data['pageNumber']) ? 0 : ((int) $data['pageNumber'] * $pagingIndex) - 2;
                $offers = array_slice($offers, $offset, $pagingIndex);
                $offers = OfferView::render($offers);
            }
            $status = '200';
        } catch (Exception $e) {
            $offers = [];
            $status = '404';
        }

        return new Response(json_encode([
            'type' => 'success',
            'status' => $status,
            'data' => $offers,
        ]));
    }
}
