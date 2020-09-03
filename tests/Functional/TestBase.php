<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\DBAL\Driver\Connection;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestBase extends WebTestCase
{
    use RecreateDatabaseTrait;

    protected static ?KernelBrowser $client = null;
    protected static ?KernelBrowser $admin = null;
    protected static ?KernelBrowser $user = null;
    protected static ?KernelBrowser $inactiveUser = null;

    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameters(
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/ld+json',
                ]
            );
        }

        if (null === self::$admin) {
            self::$admin = clone self::$client;
            $this->createAuthenticatedUser(self::$admin, 'admin@api.com');
        }

        if (null === self::$user) {
            self::$user = clone self::$client;
            $this->createAuthenticatedUser(self::$user, 'user@api.com');
        }

        if (null === self::$inactiveUser) {
            self::$inactiveUser = clone self::$client;
            $this->createAuthenticatedUser(self::$inactiveUser, 'inactive.user@api.com');
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    private function createAuthenticatedUser(KernelBrowser &$client, string $email): void
    {
        $user = self::$container->get('App\Repository\UserRepository')->findOneByEmailOrFail($email);
        $token = self::$container
            ->get('Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface')
            ->create($user);

        $client->setServerParameters(
            [
                'HTTP_Authorization' => \sprintf('Bearer %s', $token),
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );
    }

    protected function getResponseData(Response $response): array
    {
        return \json_decode($response->getContent(), true);
    }

    protected function initDbConnection(): Connection
    {
        if (null === self::$kernel) {
            self::bootKernel();
        }

        return self::$kernel->getContainer()->get('doctrine')->getConnection();
    }

    protected function getAdminId()
    {
        return $this->initDbConnection()->query('SELECT id FROM user WHERE email = "admin@api.com"')->fetchColumn(0);
    }

    protected function getUserId()
    {
        return $this->initDbConnection()->query('SELECT id FROM user WHERE email = "user@api.com"')->fetchColumn(0);
    }

    protected function getInactiveUserId()
    {
        return $this->initDbConnection()->query('SELECT id FROM user WHERE email = "inactive.user@api.com"')->fetchColumn(0);
    }
}
