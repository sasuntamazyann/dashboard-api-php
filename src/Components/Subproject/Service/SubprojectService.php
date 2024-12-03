<?php

namespace Dashboard\DashboardApi\Components\Subproject\Service;

use Doctrine\ORM\EntityManager;
use Dashboard\DashboardApi\Components\Coworker\Repository\CoworkerRepository;
use Dashboard\DashboardApi\Components\Project\Repository\ProjectRepository;
use Dashboard\DashboardApi\Components\Subproject\Subproject;

class SubprojectService
{
    private EntityManager $entityManager;

    private CoworkerRepository $coworkerRepository;

    private ProjectRepository $projectRepository;

    public function __construct(
        EntityManager        $entityManager,
        CoworkerRepository   $coworkerRepository,
        ProjectRepository    $projectRepository
    ) {
        $this->entityManager = $entityManager;
        $this->coworkerRepository = $coworkerRepository;
        $this->projectRepository = $projectRepository;
    }

    public function createSubproject(string $code, string $projectId, ?string $coworkerId): void
    {
        $coworker = null;
        if (false === is_null($coworkerId)) {
            $coworker = $this->coworkerRepository->findById($coworkerId);
        }

        $project = $this->projectRepository->getById($projectId);

        $subproject = new Subproject();
        $subproject
            ->setCode($code)
            ->setCoworker($coworker)
            ->setProject($project);
        ;

        $this->entityManager->persist($subproject);
        $this->entityManager->flush();
    }

    public function updateSubproject(
        Subproject $subproject,
        string $newCode,
        string $newProjectId,
        ?string $newCoworkerId,
    ): void {
        $newProject = $this->projectRepository->getById($newProjectId);

        $newCoworker = null;
        if (false === is_null($newCoworkerId)) {
            $newCoworker = $this->coworkerRepository->findById($newCoworkerId);
        }

        $subproject
            ->setCode($newCode)
            ->setProject($newProject)
            ->setCoworker($newCoworker);

        $this->entityManager->persist($subproject);
        $this->entityManager->flush();
    }

    public function deleteSubproject(Subproject $subproject): void
    {
        $this->entityManager->remove($subproject);
        $this->entityManager->flush();
    }
}
