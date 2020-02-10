<?php

declare(strict_types=1);

namespace App\Exception\Role;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequiredRoleToAddRoleAdminNotFoundException extends BadRequestHttpException
{
    private const MESSAGE = '%s required to perform this operation';

    public static function fromRole(string $role): self
    {
        throw new self(\sprintf(self::MESSAGE, $role));
    }
}
