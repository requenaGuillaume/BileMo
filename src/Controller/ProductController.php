<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_all_products', methods: ['GET'])]
    public function showAll(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        dd($products);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProductController.php',
        ]);
    }
}
