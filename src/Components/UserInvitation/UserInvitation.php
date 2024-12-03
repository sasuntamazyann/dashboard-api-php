<?php

namespace Dashboard\DashboardApi\Components\UserInvitation;

use DateTimeImmutable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Dashboard\DashboardApi\Components\User\User;

#[Entity, Table(name: 'user_invitations')]
class UserInvitation
{
    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'user_id', type: 'string', nullable: false)]
    private string $userId;

    #[Column(name: 'code', type: 'string', nullable: false)]
    private string $code;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[Column(name: 'verified_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $verifiedAt;

    #[OneToOne(targetEntity: User::class)]
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

    public function getVerifiedAt(): ?DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(DateTimeImmutable $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
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
}