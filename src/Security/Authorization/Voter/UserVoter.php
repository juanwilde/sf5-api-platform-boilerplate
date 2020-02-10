<?php

declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\User;
use App\Security\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends BaseVoter
{
    public const USER_READ = 'USER_READ';
    public const USER_UPDATE = 'USER_UPDATE';
    public const USER_DELETE = 'USER_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, $this->getSupportedAttributes(), true);
    }

    /**
     * @param User|null $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $tokenUser */
        $tokenUser = $token->getUser();

        if (self::USER_READ === $attribute) {
            if (null === $subject) {
                return $this->security->isGranted(Role::ROLE_ADMIN);
            }

            return $this->security->isGranted(Role::ROLE_ADMIN) || $subject->equals($tokenUser);
        }

        if (\in_array($attribute, [self::USER_UPDATE, self::USER_DELETE])) {
            return $this->security->isGranted(Role::ROLE_ADMIN) || $subject->equals($tokenUser);
        }

        return false;
    }

    private function getSupportedAttributes(): array
    {
        return [
            self::USER_READ,
            self::USER_UPDATE,
            self::USER_DELETE,
        ];
    }
}
