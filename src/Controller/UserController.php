<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        if($company !== $user->getCompany()){
            return new JsonResponse(null, JsonResponse::HTTP_UNAUTHORIZED); // or forbidden ?
        }

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/users/{userId}/company/{id}', name: 'delete_user', methods: ['DELETE'], requirements: ['userId' => '\d+', 'id' => '\d+'])]
    public function delete(?Company $company, int $userId, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        if(!$company){
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $user = $userRepository->find($userId);

        if(!$user){
            return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        if($company !== $user->getCompany()){
            return new JsonResponse(null, JsonResponse::HTTP_UNAUTHORIZED); // or forbidden ?
        }

        $company->removeUser($user);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
