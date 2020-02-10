<?php

declare(strict_types=1);

namespace App\Service\Password;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EncoderService
{
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function generateEncodedPasswordForUser(UserInterface $user, string $password, string $salt = null): string
    {
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->encodePassword($password, $salt);
    }
}
