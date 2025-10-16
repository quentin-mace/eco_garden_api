<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use App\Service\JsonValidatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createAdvice(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        JsonValidatorService $jsonValidator
    ): JsonResponse {
        $advice = $serializer->deserialize($request->getContent(), Advice::class, 'json');
        $errors = $validator->validate($advice);
        if (count($errors) > 0) {
            return $jsonValidator->serializeErrorMessages($errors);
        }
        $entityManager->persist($advice);
        $entityManager->flush();

        $jsonAdvice = $serializer->serialize($advice, 'json');

        return new JsonResponse($jsonAdvice, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: '_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateAdvice(
        Advice $advice,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        JsonValidatorService $jsonValidator
    ): JsonResponse {
        $updatedAdvice = $serializer->deserialize(
            $request->getContent(),
            Advice::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $advice ]
        );
        $errors = $validator->validate($updatedAdvice);
        if (count($errors) > 0) {
            return $jsonValidator->serializeErrorMessages($errors);
        }
        $entityManager->persist($updatedAdvice);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/{id}', name: '_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAdvice(
        Advice $advice,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $entityManager->remove($advice);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }
}
