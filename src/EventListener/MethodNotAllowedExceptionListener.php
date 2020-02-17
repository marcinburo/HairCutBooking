<?php

namespace App\EventListener;

use App\Exception\UserInputException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


class MethodNotAllowedExceptionListener
{
    const METHOD_NOT_ALLOWED = 400;

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof MethodNotAllowedHttpException)) {
            return;
        }

        $code = self::METHOD_NOT_ALLOWED;

        $responseData = [
            'error' => [
                'code' => $code,
                'message' => $exception->getMessage()
            ]
        ];

        $event->setResponse(new JsonResponse($responseData, $code));
    }
}