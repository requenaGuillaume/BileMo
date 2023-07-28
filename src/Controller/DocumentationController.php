<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SelfDiscoverabilityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentationController extends AbstractController
{
    #[Route('/', name: 'app_documentation', methods: ['GET'])]
    public function index(SelfDiscoverabilityRepository $selfDiscoverabilityRepository): Response
    {
        $discoverabilities = $selfDiscoverabilityRepository->findAll();

        $result = [];

        foreach($discoverabilities as $discoverability){
            $result[$discoverability->getResource()][] = $discoverability;
        }

        $discoverabilities = [
            'login' => $result['login'],
            'products' => $result['products'],
            'users' => $result['users']
        ];

        return $this->render('documentation/index.html.twig', ['discoverabilities' => $discoverabilities]);
    }
}
