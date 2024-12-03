<?php

namespace Dashboard\DashboardApi\Components\Project\Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Parameter;
use Dashboard\DashboardApi\Components\Client\Client;
use Dashboard\DashboardApi\Components\Project\Project;
use Dashboard\DashboardApi\Components\Project\ProjectStatus;
use Dashboard\DashboardApi\Exceptions\RecordNotFoundException;

class ProjectRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Project::class));
    }

    public function findById(string $id): ?Project
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getById(string $id): Project
    {
        $project = $this->findById($id);
        if (is_null($project)) {
            throw new RecordNotFoundException('Project not found');
        }

        return $project;
    }

    public function findByCode(string $code): ?Project
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findByName(string $name): ?Project
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function getList(int $offset, int $limit): array
    {
        $fields = [
            'p.id',
            'p.name',
            'p.code',
            'p.status',
            'p.createdAt',
            'c.id as clientId',
            'c.name as clientName',
            'cw.id as coworkerId',
            'cw.companyName as coworkerName',
        ];

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select(implode(', ', $fields))
            ->from(Project::class, 'p')
            ->join('p.client', 'c')
            ->leftJoin('p.coworker', 'cw')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function getTotalCount(): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(p.id)')
            ->from(Project::class, 'p')
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function searchByName(string $searchedName, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p.id, p.name')
            ->from(Project::class, 'p')
            ->where('p.name LIKE :searchedName')
            ->setParameter('searchedName', '%' . $searchedName . '%')
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function getListByCoworkerId(string $coworkerId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('c')
            ->from(Client::class, 'c')
            ->join('c.projects', 'p')
            ->where('p.coworkerId = :coworkerId')
            ->andWhere('p.status = :status')
            ->setParameters(
                new ArrayCollection(
                    [
                        new Parameter('coworkerId', $coworkerId),
                        new Parameter('status', ProjectStatus::STATUS_PUBLISHED->value),
                    ]
                )
            )
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}