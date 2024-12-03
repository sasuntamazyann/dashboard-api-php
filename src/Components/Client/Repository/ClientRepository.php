<?php

namespace Dashboard\DashboardApi\Components\Client\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dashboard\DashboardApi\Components\Client\Client;
use Dashboard\DashboardApi\Exceptions\RecordNotFoundException;

class ClientRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Client::class));
    }

    public function findById(string $id): ?Client
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getById(string $id): ?Client
    {
        $client = $this->findById($id);
        if (is_null($client)) {
            throw new RecordNotFoundException('Client with supplied id could not be found.');
        }

        return $client;
    }

    public function findByName(string $name): ?Client
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function getList(int $offset, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('c.id, c.name, c.createdAt')
            ->from(Client::class, 'c')
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
            ->select('COUNT(c.id)')
            ->from(Client::class, 'c')
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function searchByName(string $searchedName, int $limit): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('c.id, c.name')
            ->from(Client::class, 'c')
            ->where('c.name LIKE :searchedName')
            ->setParameter('searchedName', '%' . $searchedName . '%')
            ->setMaxResults($limit)
        ;
        $query = $queryBuilder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}