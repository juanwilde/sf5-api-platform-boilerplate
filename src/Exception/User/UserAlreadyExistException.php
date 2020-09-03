<?php

declare(strict_types=1);

namespace App\Exception\User;

class UserAlreadyExistException extends \DomainException
{
    public static function fromUserEmail(string $email): self
    {
        throw new self(\sprintf('User with email %s already exist', $email));
    }
}
