<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteUserTest extends UserTestBase
{
    public function testDeleteUserWithAdmin(): void
    {
        self::$admin->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_id'], self::FORMAT));

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteAdminWithUser(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
