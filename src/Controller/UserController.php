<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user', name: 'app_user')]
final class UserController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '_create', methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'user:write']);
        $content = $request->toArray();
        $hashedPassword = $hasher->hashPassword($user, $content['password']);
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();

        $jsonUser = $serializer->serialize([
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "zipCode" => $user->getZipCode()
            ], 'json');

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }
}
