<?php

namespace Dashboard\DashboardApi\Components\User;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\GeneratedValue;

#[Entity, Table(name: 'users')]
class User
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;
    #[Column(type: 'string', unique: true, nullable: false)]
    private string $email;

    #[Column(type: 'string', length: 60, nullable: true)]
    private ?string $password;

    #[Column(type: 'string', nullable: false, enumType: UserRole::class)]
    private UserRole $role;

    #[Column(type: 'string', nullable: false, enumType: UserStatus::class)]
    private UserStatus $status;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
