<?php

namespace Dashboard\DashboardApi\Components\Project;

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
use Dashboard\DashboardApi\Components\Client\Client;
use Dashboard\DashboardApi\Components\Coworker\Coworker;
use Dashboard\DashboardApi\Components\Subproject\Subproject;

#[Entity, Table(name: 'projects')]
class Project
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'code', type: 'string', length: 7, nullable: false)]
    private string $code;

    #[Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[Column(name: 'status', type: 'string', nullable: false, enumType: ProjectStatus::class)]
    private ProjectStatus $status;

    #[Column(name: 'client_id', type: 'string', nullable: false)]
    private string $clientId;

    #[Column(name: 'coworker_id', type: 'string', nullable: true)]
    private ?string $coworkerId;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: Client::class)]
    #[JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    private Client $client;

    #[ManyToOne(targetEntity: Coworker::class)]
    #[JoinColumn(name: 'coworker_id', referencedColumnName: 'id')]
    private ?Coworker $coworker;

    #[OneToMany(targetEntity: Subproject::class, mappedBy: 'project')]
    private Collection $subprojects;

    public function __construct()
    {
        $this->status = ProjectStatus::STATUS_DRAFT;
        $this->createdAt = new DateTimeImmutable('now');
        $this->subprojects = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(ProjectStatus $status): Project
    {
        $this->status = $status;

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

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Project
    {
        $this->client = $client;

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

    public function getSubprojects(): Collection
    {
        return $this->subprojects;
    }
}