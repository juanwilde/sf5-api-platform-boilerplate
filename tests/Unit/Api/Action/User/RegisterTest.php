<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api\Action\User;

use App\Api\Action\User\Register;
use App\Entity\User;
use App\Exception\User\UserAlreadyExistException;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RegisterTest extends TestCase
{
    /** @var UserRepository|MockObject */
    private $userRepository;

    /** @var JWTTokenManagerInterface|MockObject */
    private $JWTTokenManager;

    /** @var EncoderService|MockObject */
    private $encoderService;

    private Register $action;

    public function setUp(): void
    {
        $this->userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $this->JWTTokenManager = $this->getMockBuilder(JWTTokenManagerInterface::class)->disableOriginalConstructor()->getMock();
        $this->encoderService = $this->getMockBuilder(EncoderService::class)->disableOriginalConstructor()->getMock();

        $this->action = new Register($this->userRepository, $this->JWTTokenManager, $this->encoderService);
    }

    /**
     * @throws \Exception
     */
    public function testCreateUser(): void
    {
        $payload = [
            'name' => 'Username',
            'email' => 'username@api.com',
            'password' => 'random_password',
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByEmail')
            ->with($payload['email'])
            ->willReturn(null);

        $this->encoderService
            ->expects($this->exactly(1))
            ->method('generateEncodedPasswordForUser')
            ->with($this->isType('object'), $this->isType('string'))
            ->willReturn('encoded-password');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'));

        $this->JWTTokenManager
            ->expects($this->exactly(1))
            ->method('create')
            ->with($this->isType('object'))
            ->willReturn('jwt-token');

        $response = $this->action->__invoke($request);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testCreateUserForExistingEmail(): void
    {
        $payload = [
            'name' => 'Username',
            'email' => 'username@api.com',
            'password' => 'random_password',
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $user = new User('name', 'user@api.com');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByEmail')
            ->with($payload['email'])
            ->willReturn($user);

        $this->expectException(UserAlreadyExistException::class);

        $this->action->__invoke($request);
    }
}
