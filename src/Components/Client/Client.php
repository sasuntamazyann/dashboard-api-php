<?php

namespace Dashboard\DashboardApi\Components\Client;

use DateTimeImmutable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Dashboard\DashboardApi\Components\Project\Project;

#[Entity, Table(name: 'clients')]
class Client
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'name', type: 'string', nullable: false, length: 255)]
    private string $name;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[OneToMany(targetEntity: Project::class, mappedBy: 'client')]
    private Collection $projects;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->projects = new ArrayCollection();
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }
}