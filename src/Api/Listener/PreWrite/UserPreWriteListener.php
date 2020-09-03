<?php

declare(strict_types=1);

namespace App\Api\Listener\PreWrite;

use App\Entity\User;
use App\Security\Validator\Role\RoleValidator;
use App\Service\Password\EncoderService;
use App\Service\Request\RequestService;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class UserPreWriteListener implements PreWriteListener
{
    private const PUT_USER = 'api_users_put_item';

    private EncoderService $encoderService;

    /** @var iterable|RoleValidator[] */
    private $roleValidators;

    public function __construct(EncoderService $encoderService, iterable $roleValidators)
    {
        $this->encoderService = $encoderService;
        $this->roleValidators = $roleValidators;
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();

        if (self::PUT_USER === $request->get('_route')) {
            /** @var User $user */
            $user = $event->getControllerResult();

            $roles = [];

            foreach ($this->roleValidators as $roleValidator) {
                $roles = $roleValidator->validate($request);
            }

            $user->setRoles($roles);

            $user->setPassword(
                $this->encoderService->generateEncodedPasswordForUser(
                    $user,
                    RequestService::getField($request, 'password')
                )
            );
        }
    }
}
