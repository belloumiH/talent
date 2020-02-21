<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use App\Entity\Offer;
use Exception;


class FrontController extends AbstractController
{
    public function index(Request $request, string $languageUser)
    {
        try{
            $offers = $this->getDoctrine()
                ->getRepository(Offer::class)
                ->findBy(['enabled' => 1]);
        } catch (Exception $e){
            $offers = [];
        }
        $translations = Yaml::parseFile(
            $this->getParameter('kernel.project_dir').'/translations/translation_'.$languageUser.'.yaml'
        );
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'languageUser' => $languageUser,
            'translations' => $translations,
            'offers' => $offers,
        ]);
    }

    public function offer(Request $request, string $languageUser)
    {
        return $this->render('front/offer.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    public function error(Request $request, string $languageUser)
    {
        return $this->render('front/error.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
}
