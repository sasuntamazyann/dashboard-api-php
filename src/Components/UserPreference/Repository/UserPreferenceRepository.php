<?php

namespace Dashboard\DashboardApi\Components\UserPreference\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dashboard\DashboardApi\Components\UserPreference\UserPreference;

class UserPreferenceRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(UserPreference::class));
    }

    public function findByUserId(string $userId): ?UserPreference
    {
        return $this->findOneBy(['userId' => $userId]);
    }
}
