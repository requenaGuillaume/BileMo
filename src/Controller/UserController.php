<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/users/company/{id}', name: 'show_all_users', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showAll(UserRepository $userRepository, ?Company $company, SerializerInterface $serializer): JsonResponse
    {
        if(!$company){
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $users = $userRepository->findBy(['company' => $company]);

        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUsers, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/users/{userId}/company/{id}', name: 'show_one_user', methods: ['GET'], requirements: ['userId' => '\d+', 'id' => '\d+'])]
    public function showOne(?Company $company, int $userId, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        if(!$company){
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $user = $userRepository->find($userId);

        if(!$user){
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }
}
