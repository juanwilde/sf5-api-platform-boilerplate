<?php

declare(strict_types=1);

namespace App\Security\Validator\Role;

use App\Api\Action\RequestTransformer;
use App\Exception\Role\UnsupportedRoleException;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Request;

class AreValidRoles implements RoleValidator
{
    public function validate(Request $request): array
    {
        $roles = \array_unique(RequestTransformer::getRequiredField($request, 'roles'));

        \array_map(function (string $role): void {
            if (!\in_array($role, Role::getSupportedRoles(), true)) {
                throw UnsupportedRoleException::fromRole($role);
            }
        }, $roles);

        return $roles;
    }
}
