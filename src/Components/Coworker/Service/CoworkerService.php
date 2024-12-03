<?php

namespace Dashboard\DashboardApi\Components\Coworker\Service;
use Doctrine\ORM\EntityManager;
use Dashboard\DashboardApi\Auth\Controllers\BaseController;
use Dashboard\DashboardApi\Components\Coworker\Coworker;
use Dashboard\DashboardApi\Components\User\User;
use Dashboard\DashboardApi\Components\User\UserRole;
use Dashboard\DashboardApi\Components\User\UserStatus;
use Dashboard\DashboardApi\Components\UserInvitation\Repository\UserInvitationRepository;
use Dashboard\DashboardApi\Components\UserInvitation\Service\UserInvitationService;
use Dashboard\DashboardApi\Components\UserPreference\Service\UserPreferenceService;
use Dashboard\DashboardApi\Mailing\MailingService;

class CoworkerService
{
    private EntityManager $entityManager;
    private UserInvitationService $userInvitationService;
    private MailingService $mailingService;
    private UserPreferenceService $userPreferenceService;
    private UserInvitationRepository $userInvitationRepository;

    public function __construct(
        EntityManager $entityManager,
        UserInvitationService $userInvitationService,
        MailingService $mailingService,
        UserPreferenceService $userPreferenceService,
        UserInvitationRepository $userInvitationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userInvitationService = $userInvitationService;
        $this->mailingService = $mailingService;
        $this->userPreferenceService = $userPreferenceService;
        $this->userInvitationRepository = $userInvitationRepository;
    }

    public function createCoworker(string $companyName, string $emailAddress): void
    {
        $coworkerUser = new User();
        $coworkerUser
            ->setEmail($emailAddress)
            ->setRole(UserRole::ROLE_COWORKER)
            ->setStatus(UserStatus::STATUS_INACTIVE)
        ;

        $this->entityManager->persist($coworkerUser);
        $this->entityManager->flush();

        $coworker = new Coworker();
        $coworker
            ->setUser($coworkerUser)
            ->setCompanyName($companyName)
        ;

        $this->entityManager->persist($coworker);
        $this->entityManager->flush();
    }

    public function updateCoworker(Coworker $coworker, string $newCompanyName, string $newEmailAddress): void
    {
        $coworkerUser = $coworker->getUser();
        if ($coworkerUser->getEmail() !== $newEmailAddress) {
            $coworkerUser->setEmail($newEmailAddress);
            $coworkerUser->setStatus(UserStatus::STATUS_INACTIVE);

            $this->userInvitationRepository->deleteByUserId($coworkerUser->getId());
        }

        if ($coworker->getCompanyName() !== $newCompanyName) {
            $coworker->setCompanyName($newCompanyName);
        }

        $this->entityManager->persist($coworker);
        $this->entityManager->flush();
    }

    public function deleteCoworker(Coworker $coworker): void
    {
        $coworkerUser = $coworker->getUser();

        $this->entityManager->remove($coworkerUser);
        $this->entityManager->remove($coworker);
        $this->entityManager->flush();
    }

    public function sendInvitation(Coworker $coworker): void
    {
        $coworkerUser = $coworker->getUser();
        if ($coworkerUser->getStatus() === UserStatus::STATUS_ACTIVE) {
            return;
        }

        $invitationLink = $this->userInvitationService->createInvitation($coworkerUser);

        $userPreferenceLang = $this->userPreferenceService->getLanguagePreference($coworkerUser->getId());
        $this->mailingService->sendCoworkerInvitationEmail(
            $coworkerUser->getEmail(),
            $userPreferenceLang,
            $invitationLink,
        );
    }
}