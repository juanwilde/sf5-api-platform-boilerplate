<?php

declare(strict_types=1);

namespace App\Exception\Role;

class UnsupportedRoleException extends \DomainException
{
    public static function fromRole(string $role): self
    {
        throw new self(\sprintf('Unsupported role %s', $role));
    }
}
