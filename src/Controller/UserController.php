<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AdviceRepository;
use App\Repository\UserRepository;
use App\Service\JsonValidatorService;
use DateTime;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'app_user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly JsonValidatorService $jsonValidator,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }
    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getUsers(): JsonResponse
    {
        $users = $this->repository->findAll();
        $users = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);

        return new JsonResponse($users, Response::HTTP_OK, [], true);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '_create', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'user:write']);
        $content = $request->toArray();
        $hashedPassword = $this->hasher->hashPassword($user, $content['password']);
        $user->setPassword($hashedPassword);
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->jsonValidator->serializeErrorMessages($errors);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $jsonUser = $this->serializer->serialize([
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "zipCode" => $user->getZipCode()
            ], 'json');

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: '_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateUser(User $user, Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [
                'groups' => 'user:write',
                 AbstractNormalizer::OBJECT_TO_POPULATE => $user
            ]);
        $content = $request->toArray();
        if (isset($content['password'])) {
            $hashedPassword = $this->hasher->hashPassword($user, $content['password']);
            $user->setPassword($hashedPassword);
        }
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->jsonValidator->serializeErrorMessages($errors);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/{id}', name: '_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user): JsonResponse
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }
}
