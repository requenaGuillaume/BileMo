<?php

namespace App\Interface;

use App\Entity\Company;
use Symfony\Component\HttpFoundation\Request;

interface PaginationServiceInterface
{

    public function findProducts(Request $request): array;

    public function findUsers(Request $request, Company $company): array;

}