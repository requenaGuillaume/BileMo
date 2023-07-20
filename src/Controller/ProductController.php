<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\SelfDiscoverabilityRepository;
use App\Service\DiscoverabilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    // TODO - Authentication, cache, pagination ?
    public function __construct(
        private SerializerInterface $serializer, 
        private SelfDiscoverabilityRepository $selfDiscoverabilityRepository,
        private DiscoverabilityService $discoverabilityService
    )
    {}


    #[Route('/api/products', name: 'show_all_products', methods: ['GET'])]
    public function showAll(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        $productSelfDiscoverabilityList = $this->selfDiscoverabilityRepository->findBy(['resource' => 'products']);
        $this->discoverabilityService->setLinksForList($products, $productSelfDiscoverabilityList);

        $jsonProducts = $this->serializer->serialize($products, 'json');

        return new JsonResponse($jsonProducts, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/api/products/{id}', name: 'show_one_product', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showOne(Product $product): JsonResponse
    {
        $productSelfDiscoverabilityList = $this->selfDiscoverabilityRepository->findBy(['resource' => 'products']);
        $links = $this->discoverabilityService->getLinks($productSelfDiscoverabilityList, $product->getId());
        $product->setLinks($links);

        $jsonProduct = $this->serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, JsonResponse::HTTP_OK, [], true);
    }
}
