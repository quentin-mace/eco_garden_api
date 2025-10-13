<?php

namespace App\Controller;

use App\Repository\AdviceRepository;
use DateTime;
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
    #[Route('', name: '_current', methods: ['GET'])]
    public function getAdviceForCurrentMonth(AdviceRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $currentMonth = new DateTime();
        (int) $currentMonth = $currentMonth->format('m');
        $advices = $repository->findByMonth($currentMonth);
        $advices = $serializer->serialize($advices, 'json');

        return new JsonResponse($advices, Response::HTTP_OK, [], true);
    }


    /**
     * @throws ExceptionInterface
     */
    #[Route('/{month}', name: '_month', requirements: ['month' => '\d+'], methods: ['GET'])]
    public function getAdvicesForMonth(int $month, AdviceRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        if ($month < 1 || $month > 12) {
            return new JsonResponse(['error' => 'Invalid month. Please provide a value between 1 and 12.'], Response::HTTP_BAD_REQUEST);
        }

        $advices = $repository->findByMonth($month);
        $advices = $serializer->serialize($advices, 'json');

        return new JsonResponse($advices, Response::HTTP_OK, [], true);
    }
}
