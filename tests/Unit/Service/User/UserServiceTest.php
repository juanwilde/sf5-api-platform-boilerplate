<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\User\UserAlreadyExistException;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Mailer\MailerService;
use App\Service\Password\EncoderService;
use App\Service\User\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    /** @var UserRepository|MockObject */
    private $userRepository;

    /** @var JWTTokenManagerInterface|MockObject */
    private $JWTTokenManager;

    /** @var EncoderService|MockObject */
    private $encoderService;

    /** @var MailerService|MockObject */
    private $mailerService;

    private string $host = 'https://myapp.com';

    private UserService $userService;

    public function setUp(): void
    {
        $this->userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $this->JWTTokenManager = $this->getMockBuilder(JWTTokenManagerInterface::class)->disableOriginalConstructor()->getMock();
        $this->encoderService = $this->getMockBuilder(EncoderService::class)->disableOriginalConstructor()->getMock();
        $this->mailerService = $this->getMockBuilder(MailerService::class)->disableOriginalConstructor()->getMock();

        $this->userService = new UserService($this->userRepository, $this->JWTTokenManager, $this->encoderService, $this->mailerService, $this->host);
    }

    public function testCreateUser(): void
    {
        $payload = [
            'name' => 'Username',
            'email' => 'username@api.com',
            'password' => 'random_password',
        ];

        $this->encoderService
            ->expects($this->exactly(1))
            ->method('generateEncodedPasswordForUser')
            ->with($this->isType('object'), $this->isType('string'))
            ->willReturn('encoded-password');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'));

        $response = $this->userService->create($payload['name'], $payload['email'], $payload['password']);

        $this->assertInstanceOf(User::class, $response);
    }

    public function testCreateUserForExistingEmail(): void
    {
        $payload = [
            'name' => 'Username',
            'email' => 'username@api.com',
            'password' => 'random_password',
        ];

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'))
            ->willThrowException(new UserNotFoundException());

        $this->expectException(UserAlreadyExistException::class);

        $this->userService->create($payload['name'], $payload['email'], $payload['password']);
    }
}
