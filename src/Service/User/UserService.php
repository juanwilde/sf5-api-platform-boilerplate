<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Exception\User\UserAlreadyExistException;
use App\Repository\UserRepository;
use App\Service\Mailer\ClientRoute;
use App\Service\Mailer\MailerService;
use App\Service\Password\EncoderService;
use App\Templating\TwigTemplate;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserService
{
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $JWTTokenManager;
    private EncoderService $encoderService;
    private MailerService $mailerService;
    private string $host;

    public function __construct(
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTTokenManager,
        EncoderService $encoderService,
        MailerService $mailerService,
        string $host
    ) {
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->encoderService = $encoderService;
        $this->mailerService = $mailerService;
        $this->host = $host;
    }

    /**
     * @throws \Exception
     */
    public function create(string $name, string $email, string $password): string
    {
        $existingUser = $this->userRepository->findOneByEmail($email);
        if (null !== $existingUser) {
            throw UserAlreadyExistException::fromUserEmail($email);
        }

        $this->encoderService->validatePassword($password);

        $user = new User($name, $email);
        $user->setPassword($this->encoderService->generateEncodedPasswordForUser($user, $password));

        $this->userRepository->save($user);

        $this->sendActivationEmail($user);

        return $this->JWTTokenManager->create($user);
    }

    /**
     * @throws \Exception
     */
    private function sendActivationEmail(User $user): void
    {
        $payload = [
            'name' => $user->getName(),
            'url' => \sprintf(
                '%s%s?token=%s&uid=%s',
                $this->host,
                ClientRoute::ACTIVATE_ACCOUNT,
                $user->getToken(),
                $user->getId()
            ),
        ];

        $this->mailerService->send($user->getEmail(), TwigTemplate::USER_REGISTER, $payload);
    }
}
