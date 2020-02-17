<?php

namespace App\EventListener;

use App\Exception\BadRequestParamException;
use App\Exception\UserInputException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class BadRequestParamExceptionListener
 * @package App\EventListener
 */
class BadRequestParamExceptionListener
{
    /**
     *
     */
    const BAD_REQUEST_HTTP_CODE = 400;

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof BadRequestParamException) {
            return;
        }

        $code = self::BAD_REQUEST_HTTP_CODE;

        $responseData = [
            'error' => [
                'code' => $code,
                'message' => $exception->getMessage()
            ]
        ];

        $event->setResponse(new JsonResponse($responseData, $code));
    }
}