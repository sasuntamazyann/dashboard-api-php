<?php

namespace Dashboard\DashboardApi\Components\Subproject\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dashboard\DashboardApi\Components\Subproject\Subproject;
use Dashboard\DashboardApi\Exceptions\RecordNotFoundException;

class SubprojectRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Subproject::class));
    }

    public function findById(string $id): ?Subproject
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getById(string $id): Subproject
    {
        $subproject = $this->findById($id);
        if (is_null($subproject)) {
            throw new RecordNotFoundException("Subproject not found");
        }

        return $subproject;
    }

    public function findByCodeAndProjectId(string $code, string $projectId): ?Subproject
    {
        return $this->findOneBy(['code' => $code, 'projectId' => $projectId]);
    }

    public function getList(?string $projectId, int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(
                's.id, s.code, s.createdAt, cw.id as coworkerId, cw.companyName as coworkerName, p.id as projectId, p.name as projectName'
            )
            ->from(Subproject::class, 's')
            ->leftJoin('s.coworker', 'cw')
            ->leftJoin('s.project', 'p')
        ;

        if (false === is_null($projectId)) {
            $queryBuilder
                ->where('s.projectId = :projectId')
                ->setParameter('projectId', $projectId);
        }

        $queryBuilder->setFirstResult($offset)->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function getTotalCount(?string $projectId): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(s.id)')
            ->from(Subproject::class, 's')
            ->leftJoin('s.project', 'p')
        ;

        if (false === is_null($projectId)) {
            $queryBuilder
                ->where('s.projectId = :projectId')
                ->setParameter('projectId', $projectId);
        }

        $query = $queryBuilder->getQuery();

        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function searchByProjectId(string $searchedProjectId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('sp.id, sp.code')
            ->from(Subproject::class, 'sp')
            ->where('sp.projectId = :projectId')
            ->setParameter('projectId', $searchedProjectId)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
