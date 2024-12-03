<?php

namespace Dashboard\DashboardApi\Components\PasswordResetRequest;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Dashboard\DashboardApi\Components\User\User;

#[Entity, Table(name: 'password_reset_requests')]
final class PasswordResetRequest
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'user_id', type: 'string', nullable: false)]
    private string $userId;

    #[Column(name: 'code', type: 'string', nullable: false)]
    private string $code;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}