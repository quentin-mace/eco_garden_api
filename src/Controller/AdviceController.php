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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/advices', name: 'app_advice')]
final class AdviceController extends AbstractController
{
    public function __construct(
        private readonly AdviceRepository       $repository,
        private readonly SerializerInterface    $serializer,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
        private readonly JsonValidatorService $jsonValidator
    ){
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '_current', methods: ['GET'])]
    public function getAdviceForCurrentMonth(): JsonResponse
    {
        $currentMonth = new DateTime();
        (int) $currentMonth = $currentMonth->format('m');
        $advices = $this->repository->findByMonth($currentMonth);
        $advices = $this->serializer->serialize($advices, 'json');

        return new JsonResponse($advices, Response::HTTP_OK, [], true);
    }


    /**
     * @throws ExceptionInterface
     */
    #[Route('/{month}', name: '_month', requirements: ['month' => '\d+'], methods: ['GET'])]
    public function getAdvicesForMonth(int $month): JsonResponse
    {
        if ($month < 1 || $month > 12) {
            return new JsonResponse(['error' => 'Invalid month. Please provide a value between 1 and 12.'], Response::HTTP_BAD_REQUEST);
        }

        $advices = $this->repository->findByMonth($month);
        $advices = $this->serializer->serialize($advices, 'json');

        return new JsonResponse($advices, Response::HTTP_OK, [], true);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createAdvice(Request $request): JsonResponse
    {
        $advice = $this->serializer->deserialize($request->getContent(), Advice::class, 'json');
        $errors = $this->validator->validate($advice);
        if (count($errors) > 0) {
            return $this->jsonValidator->serializeErrorMessages($errors);
        }
        $this->entityManager->persist($advice);
        $this->entityManager->flush();

        $jsonAdvice = $this->serializer->serialize($advice, 'json');

        return new JsonResponse($jsonAdvice, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: '_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateAdvice(Advice $advice, Request $request): JsonResponse
    {
        $updatedAdvice = $this->serializer->deserialize(
            $request->getContent(),
            Advice::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $advice ]
        );
        $errors = $this->validator->validate($updatedAdvice);
        if (count($errors) > 0) {
            return $this->jsonValidator->serializeErrorMessages($errors);
        }
        $this->entityManager->persist($updatedAdvice);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/{id}', name: '_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAdvice(Advice $advice): JsonResponse {
        $this->entityManager->remove($advice);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }
}
