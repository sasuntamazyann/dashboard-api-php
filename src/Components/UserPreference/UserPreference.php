<?php

namespace Dashboard\DashboardApi\Components\UserPreference;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'user_preferences')]
class UserPreference
{
    public const DEFAULT_LANGUAGE = UserLanguage::English->value;

    #[Id, Column(type: 'string'), GeneratedValue(strategy: 'AUTO')]
    private string $id;

    #[Column(name: 'user_id', type: 'string', nullable: false)]
    private string $userId;

    #[Column(type: 'string', nullable: true, enumType: UserLanguage::class)]
    private ?UserLanguage $language;

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

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getLanguage(): ?UserLanguage
    {
        return $this->language;
    }

    public function setLanguage(UserLanguage $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}