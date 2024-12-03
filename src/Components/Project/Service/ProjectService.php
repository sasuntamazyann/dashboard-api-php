<?php

namespace Dashboard\DashboardApi\Components\Project\Service;
use Doctrine\ORM\EntityManager;
use Dashboard\DashboardApi\Components\Client\Repository\ClientRepository;
use Dashboard\DashboardApi\Components\Coworker\Repository\CoworkerRepository;
use Dashboard\DashboardApi\Components\Project\Project;
use Dashboard\DashboardApi\Components\Project\ProjectStatus;

class ProjectService
{
    private EntityManager $entityManager;

    private ClientRepository $clientRepository;

    private CoworkerRepository $coworkerRepository;

    public function __construct(
        EntityManager $entityManager,
        ClientRepository $clientRepository,
        CoworkerRepository $coworkerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->coworkerRepository = $coworkerRepository;
    }

    public function createProject(string $name, string $code, string $clientId, ?string $coworkerId): void
    {
        $client = $this->clientRepository->getById($clientId);

        $coworker = null;
        if ($coworkerId) {
            $coworker = $this->coworkerRepository->findById($coworkerId);
        }

        $project = new Project();
        $project
            ->setName($name)
            ->setCode($code)
            ->setClient($client)
            ->setCoworker($coworker)
        ;

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function updateProject(
        Project $project,
        string $newName,
        string $newCode,
        string $newClientId,
        ?string $newCoworkerId,
    ): void {
        $newClient = $this->clientRepository->getById($newClientId);

        $newCoworker = null;
        if ($newCoworkerId) {
            $newCoworker = $this->coworkerRepository->findById($newCoworkerId);
        }

        $project
            ->setCode($newCode)
            ->setName($newName)
            ->setClient($newClient)
            ->setCoworker($newCoworker);

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function deleteProject(Project $project): void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    public function publishProject(Project $montageJob): void
    {
        $montageJob->setStatus(ProjectStatus::STATUS_PUBLISHED);

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();
    }

    public function unpublishProject(Project $montageJob): void
    {
        $montageJob->setStatus(ProjectStatus::STATUS_DRAFT);

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();
    }
}