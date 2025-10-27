<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => [
                ['returnJsonException', 0],
            ],
        ];
    }

    public function returnJsonException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpExceptionInterface){
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }
        $response = new JsonResponse([
            'error' => $exception->getMessage(),
        ], $statusCode);

        $event->setResponse($response);
    }
}
