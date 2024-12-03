<?php

namespace Dashboard\DashboardApi\Components\User\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dashboard\DashboardApi\Components\User\User;
use Dashboard\DashboardApi\Components\User\UserRole;

class UserRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(User::class));
    }

    public function findByEmailAndRole(string $email, UserRole $role): ?User
    {
        $user = $this->findOneBy(['email' => $email, 'role' => $role]);

        return $user;
    }
}
