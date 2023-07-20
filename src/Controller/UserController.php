<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Company;
use App\Repository\SelfDiscoverabilityRepository;
use App\Repository\UserRepository;
use App\Service\DiscoverabilityService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends AbstractController
{
    // TODO - Authentication, cache ?
    public function __construct(
        private SerializerInterface $serializer, 
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private SelfDiscoverabilityRepository $selfDiscoverabilityRepository,
        private DiscoverabilityService $discoverabilityService
    )
    {}


    #[Route('/api/users/company/{id}', name: 'show_all_users', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showAll(Request $request, Company $company, PaginationService $paginationService): JsonResponse
    {
        $users = $paginationService->findUsers($request, $company);

        $userSelfDiscoverabilityList = $this->selfDiscoverabilityRepository->findBy(['resource' => 'users']);
        $this->discoverabilityService->setLinksForList($users, $userSelfDiscoverabilityList, true);

        $jsonUsers = $this->serializer->serialize($users, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUsers, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/api/users/{userId}/company/{id}', name: 'show_one_user', methods: ['GET'], requirements: ['userId' => '\d+', 'id' => '\d+'])]
    public function showOne(Company $company, int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);

        $this->throwExceptionIfUserNotLinkedToCompany($company, $user->getCompany());

        $userSelfDiscoverabilityList = $this->selfDiscoverabilityRepository->findBy(['resource' => 'users']);
        $links = $this->discoverabilityService->getLinks($userSelfDiscoverabilityList, $company->getId(), $userId);
        $user->setLinks($links);

        $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('/api/users/{userId}/company/{id}', name: 'delete_user', methods: ['DELETE'], requirements: ['userId' => '\d+', 'id' => '\d+'])]
    public function delete(Company $company, int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);

        $this->throwExceptionIfUserNotLinkedToCompany($company, $user->getCompany());

        $company->removeUser($user);
        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }


    #[Route('/api/users/company/{id}', name: 'create_user', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function create(
        Request $request, 
        Company $company,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $validationErrors = $validator->validate($user);
        $errorsCount = count($validationErrors);

        if($errorsCount > 0){
            $formattedErrors = $this->getFormattedErrors($validationErrors, $errorsCount);

            return new JsonResponse($this->serializer->serialize($formattedErrors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $user->setCreatedAt(DateTimeImmutable::createFromMutable(new DateTime()))
            ->setCompany($company);

        $this->em->persist($user);
        $this->em->flush();

        $userSelfDiscoverabilityList = $this->selfDiscoverabilityRepository->findBy(['resource' => 'users']);
        $links = $this->discoverabilityService->getLinks($userSelfDiscoverabilityList, $company->getId(), $user->getId());
        $user->setLinks($links);

        $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'showUsers']);

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
