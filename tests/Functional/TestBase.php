<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestBase extends WebTestCase
{
    use FixturesTrait;

    protected const FORMAT = 'jsonld';

    protected const IDS = [
        'admin_id' => '0c9a412e-2f5a-41f8-b449-6f6bcd25e001',
        'user_id' => '0c9a412e-2f5a-41f8-b449-6f6bcd25e002',
    ];

    protected static ?KernelBrowser $client = null;
    protected static ?KernelBrowser $admin = null;
    protected static ?KernelBrowser $user = null;

    /**
     * @throws ToolsException
     */
    public function setUp(): void
    {
        $this->resetDatabase();

        if (null === self::$client) {
            self::$client = static::createClient();
        }

        if (null === self::$admin) {
            self::$admin = clone self::$client;
            $this->createAuthenticatedUser(self::$admin, 'admin@api.com', 'password');
        }

        if (null === self::$user) {
            self::$user = clone self::$client;
            $this->createAuthenticatedUser(self::$user, 'user@api.com', 'password');
        }
    }

    private function createAuthenticatedUser(KernelBrowser &$client, string $username, string $password): void
    {
        $client->request(
            'POST',
            '/api/v1/users/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode(['username' => $username, 'password' => $password])
        );

        $data = \json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameters(
            [
                'HTTP_Authorization' => \sprintf('Bearer %s', $data['token']),
                'CONTENT_TYPE' => 'application/json',
            ]
        );
    }

    protected function getResponseData(Response $response): array
    {
        return \json_decode($response->getContent(), true);
    }

    /**
     * @throws ToolsException
     */
    private function resetDatabase(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!isset($metadata)) {
            $metadata = $em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();

        if (!empty($metadata)) {
            $schemaTool->createSchema($metadata);
        }

        $this->postFixtureSetup();

        $this->loadFixtures([AppFixtures::class]);
    }
}
