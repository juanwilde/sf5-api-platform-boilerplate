<?php

declare(strict_types=1);

namespace App\Exception\Password;

class PasswordException extends \DomainException
{
    public static function invalidLength(): self
    {
        throw new self('Password must be at least 6 characters');
    }
}
