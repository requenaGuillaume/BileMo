<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\ProductRepository;
use App\Interface\PaginationServiceInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class PaginationService implements PaginationServiceInterface
{

    public function __construct(
        private ProductRepository $productRepo,
        private UserRepository $userRepo
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
            $limit = intval($request->get('limit', 10));
    
            $entities = $company ? $this->userRepo->findByCompany($company, $page, $limit) : $this->productRepo->findAllWithPagination($page, $limit);
        }else{
            $entities = $company ? $this->userRepo->findBy(['company' => $company]) : $this->productRepo->findAll();
        }

        return $entities;
    }

}