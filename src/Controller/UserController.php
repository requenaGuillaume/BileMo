<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class UserController extends AbstractController
{
    // TODO - Authentication, Richardson's levels & Exception ?

    #[Route('/users/company/{id}', name: 'show_all_users', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showAll(UserRepository $userRepository, Company $company, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findBy(['company' => $company]);

        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUsers, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/users/{userId}/company/{id}', name: 'show_one_user', methods: ['GET'], requirements: ['userId' => '\d+', 'id' => '\d+'])]
    public function showOne(Company $company, int $userId, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($userId);

        $this->throwExceptionIfUserNotLinkedToCompany($company, $user->getCompany());

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/users/{userId}/company/{id}', name: 'delete_user', methods: ['DELETE'], requirements: ['userId' => '\d+', 'id' => '\d+'])]
    public function delete(Company $company, int $userId, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($userId);

        $this->throwExceptionIfUserNotLinkedToCompany($company, $user->getCompany());

        $company->removeUser($user);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }


    #[Route('/users/company/{id}', name: 'create_user', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function create(
        Request $request, 
        Company $company, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $validationErrors = $validator->validate($user);
        $errorsCount = count($validationErrors);

        if($errorsCount > 0){
            $formattedErrors = $this->getFormattedErrors($validationErrors, $errorsCount);

            return new JsonResponse($serializer->serialize($formattedErrors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $user->setCreatedAt(DateTimeImmutable::createFromMutable(new DateTime()))
            ->setCompany($company);

        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, [], true);
    }


    private function throwExceptionIfUserNotLinkedToCompany(Company $currentCompany, Company $userCompany): void
    {
        if($currentCompany !== $userCompany){
            throw new HttpException(JsonResponse::HTTP_FORBIDDEN, 'This user is not linked to this company.');
        }
    }

    private function getFormattedErrors(ConstraintViolationListInterface $validationErrors, int $errorsCount): array
    {
        $formattedErrors = ['Number of validation errors' => $errorsCount];

        foreach($validationErrors as $error){
            $formattedErrors[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }

        return $formattedErrors;
    }
}
