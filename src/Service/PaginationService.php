<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\ProductRepository;
use App\Interface\PaginationServiceInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PaginationService implements PaginationServiceInterface
{

    public function __construct(
        private ProductRepository $productRepo,
        private UserRepository $userRepo,
        private TagAwareCacheInterface $cachePool
    ){}

    public function findProducts(Request $request): array
    {
        return $this->find($request);
    }

    public function findUsers(Request $request, Company $company): array
    {
        return $this->find($request, $company);
    }

    private function find(Request $request, ?Company $company = null): array
    {
        $page = intval($request->get('page'));

        if($page){
            // TODO add cache with pagination
            $limit = intval($request->get('limit', 10));
    
            $entities = $company ? $this->userRepo->findByCompany($company, $page, $limit) : $this->productRepo->findAllWithPagination($page, $limit);
        }else{

            if($company){
                $companyId = $company->getId();
                $cacheId = "users-classic-$companyId";

                $entities = $this->cachePool->get($cacheId, function(ItemInterface $item) use($company, $companyId){
                    $item->tag("users-$companyId");
                    return $this->userRepo->findBy(['company' => $company]);
                });
            }else{
                $cacheId = "products-classic";

                $entities = $this->cachePool->get($cacheId, function(ItemInterface $item){
                    $item->tag('products');
                    return $this->productRepo->findAll();
                });
            }
        }

        return $entities;
    }

}