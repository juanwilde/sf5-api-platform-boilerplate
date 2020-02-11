<?php

declare(strict_types=1);

namespace App\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use App\Security\Role;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class DoctrineUserExtension implements QueryCollectionExtensionInterface
{
    private TokenStorageInterface $tokenStorage;
    private Security $security;

    public function __construct(TokenStorageInterface $tokenStorage, Security $security)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $qb, string $resourceClass): void
    {
        if ($this->security->isGranted(Role::ROLE_ADMIN)) {
            return;
        }

        // Add you custom logic here to manage sub resources like GET /users/{id}/entity
        // https://api-platform.com/docs/core/extensions/#custom-doctrine-orm-extension
    }

    private function getResources(): array
    {
        return [User::class => 'id'];
    }
}
