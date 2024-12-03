<?php

namespace Dashboard\DashboardApi\Components\User\Service;

use Doctrine\ORM\EntityManager;
use Dashboard\DashboardApi\Components\User\User;

class UserService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function changePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        if (false === password_verify($oldPassword, $user->getPassword())) {
            return false;
        }

        $user->setPassword(
            $this->hashPassword($newPassword)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
