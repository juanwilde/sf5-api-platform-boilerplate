<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return User::class;
    }

    public function findOneByEmail(string $email): ?User
    {
        /** @var User $user */
        $user = $this->objectRepository->findOneBy(['email' => $email]);

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
