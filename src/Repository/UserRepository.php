<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return User::class;
    }

    public function findOneByEmailOrFail(string $email): User
    {
        if (null === $user = $this->objectRepository->findOneBy(['email' => $email])) {
            throw UserNotFoundException::fromEmail($email);
        }

        return $user;
    }

    public function save(UserInterface $user): void
    {
        $this->saveEntity($user);
    }

    public function remove(UserInterface $user): void
    {
        $this->removeEntity($user);
    }
}
