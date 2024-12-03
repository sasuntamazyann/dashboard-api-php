<?php

namespace Dashboard\DashboardApi\Components\UserPreference\Service;
use Doctrine\ORM\EntityManager;
use Dashboard\DashboardApi\Components\UserPreference\Repository\UserPreferenceRepository;
use Dashboard\DashboardApi\Components\UserPreference\UserLanguage;
use Dashboard\DashboardApi\Components\UserPreference\UserPreference;

class UserPreferenceService
{
    private UserPreferenceRepository $userPreferenceRepository;

    private EntityManager $entityManager;

    public function __construct(UserPreferenceRepository $userPreferenceRepository, EntityManager $entityManager)
    {
        $this->userPreferenceRepository = $userPreferenceRepository;
        $this->entityManager = $entityManager;
    }

    public function setLanguagePreference(string $userId, string $languagePreference): void
    {
        $userPreference = $this->userPreferenceRepository->findByUserId($userId);
        if (is_null($userPreference)) {
            $userPreference = new UserPreference();
            $userPreference->setUserId($userId);
        }

        $userPreference->setLanguage(UserLanguage::from($languagePreference));

        $this->entityManager->persist($userPreference);
        $this->entityManager->flush();
    }

    public function getLanguagePreference(string $userId): string
    {
        $userPreference = $this->userPreferenceRepository->findByUserId($userId);
        if (is_null($userPreference)) {
            return UserPreference::DEFAULT_LANGUAGE;
        }

        return $userPreference->getLanguage()->value;
    }
}
