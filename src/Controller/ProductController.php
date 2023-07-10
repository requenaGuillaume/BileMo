<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    // TODO - Authentication, Richardson's levels & Exception ?

    #[Route('/products', name: 'show_all_products', methods: ['GET'])]
    public function showAll(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $products = $productRepository->findAll();

        $jsonProducts = $serializer->serialize($products, 'json');

        return new JsonResponse($jsonProducts, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/products/{id}', name: 'show_one_product', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showOne(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, JsonResponse::HTTP_OK, [], true);
    }
}
