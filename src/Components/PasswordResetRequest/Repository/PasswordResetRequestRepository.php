<?php

namespace Dashboard\DashboardApi\Components\PasswordResetRequest\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dashboard\DashboardApi\Components\PasswordResetRequest\PasswordResetRequest;

class PasswordResetRequestRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(PasswordResetRequest::class));
    }

    public function findByCode(string $code): ?PasswordResetRequest
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findByUserId(string $userId): ?PasswordResetRequest
    {
        return $this->findOneBy(['userId' => $userId]);
    }
}

