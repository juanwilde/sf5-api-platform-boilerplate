<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Validator\Role;

use App\Exception\Role\UnsupportedRoleException;
use App\Security\Role;
use App\Security\Validator\Role\AreValidRoles;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AreValidRolesTest extends TestCase
{
    private AreValidRoles $validator;

    public function setUp(): void
    {
        $this->validator = new AreValidRoles();
    }

    public function testRolesAreValid(): void
    {
        $payload = [
            'roles' => [
                Role::ROLE_ADMIN,
                Role::ROLE_USER,
            ],
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $response = $this->validator->validate($request);

        $this->assertIsArray($response);
    }

    public function testInvalidRoles(): void
    {
        $payload = [
            'roles' => [
                Role::ROLE_ADMIN,
                'ROLE_FAKE',
            ],
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $this->expectException(UnsupportedRoleException::class);
        $this->expectExceptionMessage('Unsupported role ROLE_FAKE');

        $this->validator->validate($request);
    }
}
