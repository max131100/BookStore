<?php

namespace App\Listener;

use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class ApiExceptionListener
{
    public function __construct(
        private ExceptionMappingResolver $resolver,
        private LoggerInterface $logger,
        private SerializerInterface $serializer)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($this->isSecurityException($throwable)) {
            return;
        }


        $mapping = $this->resolver->resolve(get_class($throwable));
        if ($mapping === null) {
            $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error($throwable->getMessage(), [
                'trace' => $throwable->getTraceAsString(),
                'previous' => $throwable->getPrevious() !== null ? $throwable->getPrevious()->getMessage() : '',
            ]);
        }

        $message = $mapping->isHidden() ? Response::$statusTexts[$mapping->getCode()] : $throwable->getMessage();
        $details = new ErrorDebugDetails($throwable->getTraceAsString());
        $data = $this->serializer->serialize(new ErrorResponse($message, $details), JsonEncoder::FORMAT);
        $response = new JsonResponse($data, $mapping->getCode(), [], true);

        $event->setResponse($response);
    }

    private function isSecurityException(Throwable $throwable): bool
    {
        return $throwable instanceof AuthenticationException;
    }
}
