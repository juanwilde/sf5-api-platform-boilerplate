<?php

declare(strict_types=1);

namespace App\Exception\Role;

class RequiredRoleToAddRoleAdminNotFoundException extends \DomainException
{
    public static function fromRole(string $role): self
    {
        throw new self(\sprintf('%s required to perform this operation', $role));
    }
}
