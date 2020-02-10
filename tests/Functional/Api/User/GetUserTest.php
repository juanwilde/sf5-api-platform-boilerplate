<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserTest extends UserTestBase
{
    public function testGetUsersForAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, $responseData['hydra:member']);
    }

    public function testGetUsersForUser(): void
    {
        self::$user->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetUserWithAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_id'], self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(self::IDS['user_id'], $responseData['id']);
    }

    public function testGetAdminWithUser(): void
    {
        self::$user->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
