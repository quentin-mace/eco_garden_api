<?php

namespace App\Controller;

use App\Repository\AdviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/advice', name: 'app_advice')]
final class AdviceController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '', methods: ['GET'])]
    public function index(AdviceRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $advices = $repository->findAll();
        $advices = $serializer->serialize($advices, 'json');

        return new JsonResponse($advices, Response::HTTP_OK, [], true);
    }
}
