<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use App\Security\Role;
use Symfony\Component\HttpFoundation\JsonResponse;

class PutUserTest extends UserTestBase
{
    public function testPutUserWithAdmin(): void
    {
        $payload = [
            'name' => 'New name',
            'password' => 'password2',
            'roles' => [
                Role::ROLE_ADMIN,
                Role::ROLE_USER,
            ],
        ];

        $userId = $this->getUserId();

        self::$admin->request('PUT', \sprintf('%s/%s', $this->endpoint, $userId), [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($userId, $responseData['id']);
        $this->assertEquals($payload['name'], $responseData['name']);
        $this->assertEquals($payload['roles'], $responseData['roles']);
    }

    public function testPutAdminWithUser(): void
    {
        $payload = [
            'name' => 'New name',
            'password' => 'password2',
            'roles' => [
                Role::ROLE_ADMIN,
                Role::ROLE_USER,
            ],
        ];

        self::$user->request('PUT', \sprintf('%s/%s', $this->endpoint, $this->getAdminId()), [], [], [], \json_encode($payload));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPutUserWithAdminAndFakeRole(): void
    {
        $payload = [
            'name' => 'New name',
            'password' => 'password2',
            'roles' => [
                Role::ROLE_ADMIN,
                'ROLE_FAKE',
            ],
        ];

        self::$admin->request('PUT', \sprintf('%s/%s', $this->endpoint, $this->getUserId()), [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testAddAdminRoleWithUser(): void
    {
        $payload = [
            'name' => 'New name',
            'password' => 'password2',
            'roles' => [
                Role::ROLE_ADMIN,
                Role::ROLE_USER,
            ],
        ];

        self::$user->request('PUT', \sprintf('%s/%s', $this->endpoint, $this->getUserId()), [], [], [], \json_encode($payload));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
