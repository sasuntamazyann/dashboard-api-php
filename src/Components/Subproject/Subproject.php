<?php

namespace Dashboard\DashboardApi\Components\Subproject;

use DateTimeImmutable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Dashboard\DashboardApi\Components\Coworker\Coworker;
use Dashboard\DashboardApi\Components\Nvt\Nvt;
use Dashboard\DashboardApi\Components\Project\Project;

#[Entity, Table(name: 'subprojects')]
class Subproject
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'project_id', type: 'string', nullable: false)]
    private string $projectId;

    #[ManyToOne(targetEntity: Project::class)]
    #[JoinColumn(name: 'project_id', referencedColumnName: 'id')]
    private Project $project;

    #[Column(name: 'code', type: 'string', length: 7, nullable: false)]
    private string $code;

    #[ManyToOne(targetEntity: Coworker::class)]
    #[JoinColumn(name: 'coworker_id', referencedColumnName: 'id')]
    private ?Coworker $coworker;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[OneToMany(targetEntity: Nvt::class, mappedBy: 'subproject')]
    private Collection $nvts;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->nvts = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function setProjectId(string $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCoworker(): ?Coworker
    {
        return $this->coworker;
    }

    public function setCoworker(?Coworker $coworker): self
    {
        $this->coworker = $coworker;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getNvts(): Collection
    {
        return $this->nvts;
    }
}
