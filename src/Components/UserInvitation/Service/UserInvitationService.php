<?php

namespace Dashboard\DashboardApi\Components\UserInvitation\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Dashboard\DashboardApi\Auth\Controllers\BaseController;
use Dashboard\DashboardApi\Components\User\Service\UserService;
use Dashboard\DashboardApi\Components\User\User;
use Dashboard\DashboardApi\Components\User\UserStatus;
use Dashboard\DashboardApi\Components\UserInvitation\Repository\UserInvitationRepository;
use Dashboard\DashboardApi\Components\UserInvitation\UserInvitation;
use Dashboard\DashboardApi\Components\UserInvitation\UserInvitationStatus;
use Dashboard\DashboardApi\Exceptions\RecordNotFoundException;
use Dashboard\DashboardApi\Utility\StringUtility;

class UserInvitationService
{
    private EntityManager $entityManager;
    private UserInvitationRepository $userInvitationRepository;
    private UserService $userService;

    public function __construct(
        EntityManager $entityManager,
        UserInvitationRepository $userInvitationRepository,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->userInvitationRepository = $userInvitationRepository;
        $this->userService = $userService;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createInvitation(User $user): string
    {
        $userInvitation = $this->userInvitationRepository->findByUserId($user->getId());
        if (false === is_null($userInvitation)) {
            if ($userInvitation->getVerifiedAt()) {
                throw new \RuntimeException('User has already accepted invitation.');
            }

            $userInvitation->setCode(StringUtility::generateRandomString(32));
        } else {
            $userInvitation = new UserInvitation();
            $userInvitation
                ->setUser($user)
                ->setCode(StringUtility::generateRandomString(32));
        }

        $this->entityManager->persist($userInvitation);
        $this->entityManager->flush();

        return $this->buildInvitationLink($userInvitation->getCode());
    }

    public function getInvitationStatus(string $userId): UserInvitationStatus
    {
        $userInvitation = $this->userInvitationRepository->findByUserId($userId);
        if (is_null($userInvitation)) {
            return UserInvitationStatus::NOT_SENT;
        }

        if (is_null($userInvitation->getVerifiedAt())) {
            return UserInvitationStatus::PENDING;
        }

        return UserInvitationStatus::ACCEPTED;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws RecordNotFoundException
     */
    public function acceptInvitation(string $invitationAcceptCode, string $password): void
    {
        $userInvitation = $this->userInvitationRepository->getByCode($invitationAcceptCode);
        if ($userInvitation->getVerifiedAt()) {
            return;
        }

        $user = $userInvitation->getUser();

        $newPasswordHash = $this->userService->hashPassword($password);
        $user
            ->setPassword($newPasswordHash)
            ->setStatus(UserStatus::STATUS_ACTIVE);

        $this->entityManager->persist($user);

        $userInvitation->setVerifiedAt(new \DateTimeImmutable('now'));

        $this->entityManager->persist($userInvitation);
        $this->entityManager->flush();
    }

    private function buildInvitationLink(string $invitationCode): string
    {
        return "{$_ENV['WEBSITE_DOMAIN']}/accept-invite?code={$invitationCode}";
    }
}
