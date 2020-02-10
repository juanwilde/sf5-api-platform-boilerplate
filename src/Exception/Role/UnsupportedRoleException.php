<?php

declare(strict_types=1);

namespace App\Exception\Role;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UnsupportedRoleException extends BadRequestHttpException
{
    private const MESSAGE = 'Unsupported role %s';

    public static function fromRole(string $role): self
    {
        throw new self(\sprintf(self::MESSAGE, $role));
    }
}
