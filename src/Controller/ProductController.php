<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\PaginationService;
use App\Service\DiscoverabilityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SelfDiscoverabilityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    // TODO - cache ?
    public function __construct(
        private SerializerInterface $serializer, 
        private SelfDiscoverabilityRepository $selfDiscoverabilityRepository,
        private DiscoverabilityService $discoverabilityService
    )
    {}


    #[Route('/api/products', name: 'show_all_products', methods: ['GET'])]
    public function showAll(Request $request, PaginationService $paginationService): JsonResponse
    {
        $products = $paginationService->findProducts($request);

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
