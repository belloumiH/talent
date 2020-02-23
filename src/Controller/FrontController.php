<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Offer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class FrontController extends AbstractController
{
    public function index(Request $request, string $languageUser)
    {
        try {
            $offers = $this->getDoctrine()
                ->getRepository(Offer::class)
                ->findBy(['enabled' => 1]);
        } catch (Exception $e) {
            $offers = [];
        }
        $translations = Yaml::parseFile(
            $this->getParameter('kernel.project_dir').'/translations/translation_'.$languageUser.'.yaml'
        );

        return $this->render('front/index.twig', [
            'controller_name' => 'FrontController',
            'languageUser' => $languageUser,
            'translations' => $translations,
            'offers' => $offers,
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
            $fileName = '';
            if (null !== $candidateFile) {
                $fileName = microtime(true).'.'.$candidateFile->guessExtension();
                try {
                    $candidateFile->move(
                        $this->getParameter('uploads_private_directory'),
                        $fileName);
                } catch (Exception $e) {
                    $fileName = null;
                }
            }
            $candidate = new Candidate();
            $candidate->setFirstName((string) $data['your-first-name']);
            $candidate->setLastName((string) $data['your-name']);
            $candidate->setPhone((string) $data['phone']);
            $candidate->setMail((string) $data['your-email']);
            $candidate->setComment((string) $data['message']);
            $candidate->setFile((string) $fileName);
            $offer = $this->getDoctrine()
                ->getRepository(Offer::class)
                ->find((int) $data['offer-id']);
            $candidate->setOffer($offer);
            $this->insert($candidate);
        }

        return $this->redirectToRoute('index', ['languageUser' => $languageUser]);
    }

    public function error(Request $request, string $languageUser)
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
}
