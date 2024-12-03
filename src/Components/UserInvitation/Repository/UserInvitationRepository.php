<?php

namespace Dashboard\DashboardApi\Components\UserInvitation\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dashboard\DashboardApi\Components\UserInvitation\UserInvitation;
use Dashboard\DashboardApi\Exceptions\RecordNotFoundException;

class UserInvitationRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(UserInvitation::class));
    }

    public function findByUserId(string $userId): ?UserInvitation
    {
        return $this->findOneBy(['userId' => $userId]);
    }

    public function getByCode(string $code): UserInvitation
    {
        $userInvitation = $this->findOneBy(['code' => $code]);
        if (is_null($userInvitation)) {
            throw new RecordNotFoundException('User invitation not found');
        }

        return $userInvitation;
    }

    public function deleteByUserId(string $userId): void
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->delete(UserInvitation::class, 'u')
            ->where('u.userId = :userId')
            ->setParameter('userId', $userId);
        ;
        $query = $queryBuilder->getQuery();
        $query->execute();
    }
}