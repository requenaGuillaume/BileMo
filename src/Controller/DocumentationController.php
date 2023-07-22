<?php

namespace App\Controller;

use App\Repository\SelfDiscoverabilityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    #[Route('/', name: 'app_documentation', methods: ['GET'])]
    public function index(SelfDiscoverabilityRepository $selfDiscoverabilityRepository): Response
    {
        $discoverabilities = [
            'login' => $selfDiscoverabilityRepository->findBy(['resource' => 'login']),
            'products' => $selfDiscoverabilityRepository->findBy(['resource' => 'products']),
            'users' => $selfDiscoverabilityRepository->findBy(['resource' => 'users'])
        ];

        return $this->render('documentation/index.html.twig', ['discoverabilities' => $discoverabilities]);
    }
}
