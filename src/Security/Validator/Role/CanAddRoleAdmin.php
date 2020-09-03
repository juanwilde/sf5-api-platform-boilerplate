<?php

declare(strict_types=1);

namespace App\Security\Validator\Role;

use App\Exception\Role\RequiredRoleToAddRoleAdminNotFoundException;
use App\Security\Role;
use App\Service\Request\RequestService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class CanAddRoleAdmin implements RoleValidator
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate(Request $request): array
    {
        $roles = \array_unique(RequestService::getField($request, 'roles'));

        if (\in_array(Role::ROLE_ADMIN, $roles, true)) {
            if (!$this->security->isGranted(Role::ROLE_ADMIN)) {
                throw RequiredRoleToAddRoleAdminNotFoundException::fromRole(Role::ROLE_ADMIN);
            }
        }

        return $roles;
    }
}
