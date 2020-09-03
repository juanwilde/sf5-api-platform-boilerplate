<?php

declare(strict_types=1);

namespace App\Api\Listener;

use App\Exception\Password\PasswordException;
use App\Exception\Role\RequiredRoleToAddRoleAdminNotFoundException;
use App\Exception\Role\UnsupportedRoleException;
use App\Exception\User\UserAlreadyExistException;
use App\Exception\User\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JsonExceptionResponseTransformerListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $exceptionClass = \get_class($exception);

        $data = [
            'class' => $exceptionClass,
            'message' => $exception->getMessage(),
        ];

        if (\in_array($exceptionClass, $this->getBadRequestExceptions(), true)) {
            $data['code'] = JsonResponse::HTTP_BAD_REQUEST;
        }

        if (\in_array($exceptionClass, $this->getConflictExceptions(), true)) {
            $data['code'] = JsonResponse::HTTP_CONFLICT;
        }

        if (\in_array($exceptionClass, $this->getNotFoundExceptions(), true)) {
            $data['code'] = JsonResponse::HTTP_NOT_FOUND;
        }

        if ($exception instanceof AccessDeniedException) {
            $data['code'] = JsonResponse::HTTP_FORBIDDEN;
        }

        $event->setResponse($this->prepareResponse($data));
    }

    private function prepareResponse(array $data): JsonResponse
    {
        $response = new JsonResponse($data, $data['code']);
        $response->headers->set('Server-Time', \time());
        $response->headers->set('X-Error-Code', $data['code']);

        return $response;
    }

    private function getBadRequestExceptions(): array
    {
        return [
            PasswordException::class,
            RequiredRoleToAddRoleAdminNotFoundException::class,
            UnsupportedRoleException::class,
        ];
    }

    private function getConflictExceptions(): array
    {
        return [UserAlreadyExistException::class];
    }

    private function getNotFoundExceptions(): array
    {
        return [UserNotFoundException::class];
    }
}
