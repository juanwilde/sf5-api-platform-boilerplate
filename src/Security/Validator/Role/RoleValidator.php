<?php

declare(strict_types=1);

namespace App\Security\Validator\Role;

use Symfony\Component\HttpFoundation\Request;

interface RoleValidator
{
    public function validate(Request $request): array;
}
