<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class JsonValidatorService
{
    public function serializeErrorMessages(ConstraintViolationListInterface $errors): JsonResponse
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        $jsonError = $serializer->serialize($messages, 'json');

        return new JsonResponse($jsonError, Response::HTTP_BAD_REQUEST, [], true);
    }
}
