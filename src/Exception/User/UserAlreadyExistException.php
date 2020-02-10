<?php

declare(strict_types=1);

namespace App\Exception\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAlreadyExistException extends BadRequestHttpException
{
    private const MESSAGE = 'User with email %s already exist';

    public static function fromUserEmail(string $email): self
    {
        throw new self(\sprintf(self::MESSAGE, $email));
    }
}
