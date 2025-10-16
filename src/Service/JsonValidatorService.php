<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class JsonValidatorService
{
    public function __construct(
        private SerializerInterface $serializer,
    ){
    }

    /**
     * @throws ExceptionInterface
     */
    public function serializeErrorMessages(ConstraintViolationListInterface $errors): JsonResponse
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        $jsonError = $this->serializer->serialize($messages, 'json');

        return new JsonResponse($jsonError, Response::HTTP_BAD_REQUEST, [], true);
    }
}
