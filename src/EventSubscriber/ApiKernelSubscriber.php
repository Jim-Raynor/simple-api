<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiKernelSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;
    private LoggerInterface $logger;
    private array $contentTypeToResponseFormatMap;
    private bool $isDebug;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['logException', 10],
                ['showException', 0],
            ],
            KernelEvents::VIEW => 'showControllerResult',
        ];
    }

    public function __construct(
        SerializerInterface $serializer,
        LoggerInterface $logger,
        array $contentTypeToResponseFormatMap,
        bool $isDebug
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->contentTypeToResponseFormatMap = $contentTypeToResponseFormatMap;
        $this->isDebug = $isDebug;
    }

    public function logException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ValidationFailedException) {
            $this->logger->error($exception);
        }
    }

    public function showException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($this->isDebug && !$exception instanceof ValidationFailedException) {
            return;
        }

        $errorData = null;
        if ($exception instanceof HttpExceptionInterface) {
            $httpCode = $exception->getStatusCode();
            $error = 'HTTP error';
            $errorData = $exception->getMessage();
        } elseif ($exception instanceof ValidationFailedException) {
            $httpCode = Response::HTTP_BAD_REQUEST;
            $error = 'validation error';
            $errorData = $exception->getViolations();
        } else {
            $error = 'internal error';
            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $responseData = [
            'success' => false,
            'code' => $exception->getCode(),
            'error' => $error,
        ];
        if (null !== $errorData) {
            $responseData['data'] = $errorData;
        }

        $response = $this->createResponse($event, $httpCode, $responseData);

        $event->setResponse($response);
    }

    public function showControllerResult(ViewEvent $event): void
    {
        $responseData = [
            'success' => true,
            'result' => $event->getControllerResult(),
        ];
        $response = $this->createResponse($event, Response::HTTP_OK, $responseData);

        $event->setResponse($response);
    }

    private function createResponse(RequestEvent $event, int $httpCode, array $responseData): Response
    {
        $contentType = $this->selectContentType($event->getRequest());
        $responseFormat = $this->contentTypeToResponseFormatMap[$contentType];
        $responseBody = $this->serializer->serialize($responseData, $responseFormat);

        return new Response($responseBody, $httpCode, ['Content-Type' => $contentType]);
    }

    private function selectContentType(Request $request): string
    {
        foreach ($request->getAcceptableContentTypes() as $acceptableContentType) {
            if (isset($this->contentTypeToResponseFormatMap[$acceptableContentType])) {
                return $acceptableContentType;
            }
        }

        reset($this->contentTypeToResponseFormatMap);

        return key($this->contentTypeToResponseFormatMap);
    }
}
