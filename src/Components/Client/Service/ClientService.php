<?php

namespace Dashboard\DashboardApi\Components\Client\Service;

use Doctrine\ORM\EntityManager;
use Dashboard\DashboardApi\Components\Client\Client;

class ClientService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createClient(string $name): void
    {
        $client = new Client();
        $client->setName($name);

        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function updateClient(Client $client, string $newName): void
    {
        $client->setName($newName);

        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function deleteClient(Client $client): void
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }
}