<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\Role;
use App\Service\Password\EncoderService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private EncoderService $encoderService;

    public function __construct(EncoderService $encoderService)
    {
        $this->encoderService = $encoderService;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $users = $this->getUsers();
        foreach ($users as $userData) {
            $user = new User($userData['name'], $userData['email'], $userData['id']);
            $user->setPassword($this->encoderService->generateEncodedPasswordForUser($user, $userData['password']));
            $user->setRoles($userData['roles']);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUsers(): array
    {
        return [
            [
                'id' => '0c9a412e-2f5a-41f8-b449-6f6bcd25e001',
                'name' => 'Admin',
                'email' => 'admin@api.com',
                'password' => 'password',
                'roles' => [
                    Role::ROLE_ADMIN,
                    Role::ROLE_USER,
                ],
            ],
            [
                'id' => '0c9a412e-2f5a-41f8-b449-6f6bcd25e002',
                'name' => 'User',
                'email' => 'user@api.com',
                'password' => 'password',
                'roles' => [Role::ROLE_USER],
            ],
        ];
    }
}
