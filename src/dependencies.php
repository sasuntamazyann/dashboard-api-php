<?php

namespace Dashboard\RiconasApi;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

return [
    'db-config' => require __DIR__ . '/../configs/db.config.php',
    'jwt-config' => require __DIR__ . '/../configs/jwt.config.php',
    'mail-config' => require __DIR__ . '/../configs/mail.config.php',
    Integrations\Firebase\Jwt\JwtEncoder::class => function (ContainerInterface $c) {
        return new Integrations\Firebase\Jwt\JwtEncoder($c->get('jwt-config'));
    },
    Integrations\Firebase\Jwt\JwtDecoder::class => function (ContainerInterface $c) {
        return new Integrations\Firebase\Jwt\JwtDecoder($c->get('jwt-config'));
    },
    Integrations\Mailgun\MailgunClient::class => function (ContainerInterface $c) {
        return new Integrations\Mailgun\MailgunClient($c->get('mail-config'));
    },
    EntityManager::class => function (ContainerInterface $c) {
        $dbConfigs = $c->get('db-config');
        $dbParams = [
            'driver' => 'pdo_mysql',
            'user' => $dbConfigs['username'],
            'password' => $dbConfigs['password'],
            'dbname' => $dbConfigs['database'],
        ];

        $config = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
            ['/'],
            $_ENV['APP_DEBUG'] === "true",
            __DIR__ . '/../data/doctrine/proxies',
        );
        $connection = \Doctrine\DBAL\DriverManager::getConnection($dbParams, $config);

        return new EntityManager($connection, $config);
    },
    Components\Client\Repository\ClientRepository::class => function (ContainerInterface $c) {
        return new Components\Client\Repository\ClientRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\Client\Service\ClientService::class => function (ContainerInterface $c) {
        return new Components\Client\Service\ClientService(
            $c->get(EntityManager::class),
        );
    },
    Components\Coworker\Repository\CoworkerRepository::class => function (ContainerInterface $c) {
        return new Components\Coworker\Repository\CoworkerRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\Coworker\Service\CoworkerService::class => function (ContainerInterface $c) {
        return new Components\Coworker\Service\CoworkerService(
            $c->get(EntityManager::class),
            $c->get(Components\UserInvitation\Service\UserInvitationService::class),
            $c->get(Mailing\MailingService::class),
            $c->get(Components\UserPreference\Service\UserPreferenceService::class),
            $c->get(Components\UserInvitation\Repository\UserInvitationRepository::class),
        );
    },
    Components\User\Repository\UserRepository::class => function (ContainerInterface $c) {
        return new Components\User\Repository\UserRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\User\Service\UserService::class => function (ContainerInterface $c) {
        return new Components\User\Service\UserService(
            $c->get(EntityManager::class),
        );
    },
    Components\UserInvitation\Repository\UserInvitationRepository::class => function (ContainerInterface $c) {
        return new Components\UserInvitation\Repository\UserInvitationRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\UserInvitation\Service\UserInvitationService::class => function (ContainerInterface $c) {
        return new Components\UserInvitation\Service\UserInvitationService(
            $c->get(EntityManager::class),
            $c->get(Components\UserInvitation\Repository\UserInvitationRepository::class),
            $c->get(Components\User\Service\UserService::class),
        );
    },
    Components\UserPreference\Repository\UserPreferenceRepository::class => function (ContainerInterface $c) {
        return new Components\UserPreference\Repository\UserPreferenceRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\UserPreference\Service\UserPreferenceService::class => function (ContainerInterface $c) {
        return new Components\UserPreference\Service\UserPreferenceService(
            $c->get(Components\UserPreference\Repository\UserPreferenceRepository::class),
            $c->get(EntityManager::class),
        );
    },
    Components\PasswordResetRequest\Repository\PasswordResetRequestRepository::class => function (ContainerInterface $c) {
        return new Components\PasswordResetRequest\Repository\PasswordResetRequestRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\PasswordResetRequest\Service\PasswordResetRequestService::class => function (ContainerInterface $c) {
        return new Components\PasswordResetRequest\Service\PasswordResetRequestService(
            $c->get(EntityManager::class),
            $c->get(Components\PasswordResetRequest\Repository\PasswordResetRequestRepository::class),
            $c->get(Components\User\Service\UserService::class),
            $c->get(Mailing\MailingService::class),
            $c->get(Components\UserPreference\Service\UserPreferenceService::class),
        );
    },
    Components\Project\Repository\ProjectRepository::class => function (ContainerInterface $c) {
        return new Components\Project\Repository\ProjectRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\Project\Service\ProjectService::class => function (ContainerInterface $c) {
        return new Components\Project\Service\ProjectService(
            $c->get(EntityManager::class),
            $c->get(Components\Client\Repository\ClientRepository::class),
            $c->get(Components\Coworker\Repository\CoworkerRepository::class),
        );
    },
    Components\Subproject\Repository\SubprojectRepository::class => function (ContainerInterface $c) {
        return new Components\Subproject\Repository\SubprojectRepository(
            $c->get(EntityManager::class),
        );
    },
    Components\Subproject\Service\SubprojectService::class => function (ContainerInterface $c) {
        return new Components\Subproject\Service\SubprojectService(
            $c->get(EntityManager::class),
            $c->get(Components\Coworker\Repository\CoworkerRepository::class),
            $c->get(Components\Project\Repository\ProjectRepository::class),
        );
    },
    Authentication\AuthenticationService::class => function (ContainerInterface $c) {
        return new Authentication\AuthenticationService(
            $c->get(Integrations\Firebase\Jwt\JwtEncoder::class),
            $c->get(Integrations\Firebase\Jwt\JwtDecoder::class),
            $c->get(Components\User\Repository\UserRepository::class),
        );
    },
    Mailing\MailingService::class => function (ContainerInterface $c) {
        return new Mailing\MailingService(
            $c->get(Integrations\Mailgun\MailgunClient::class),
            $c->get('mail-config'),
        );
    }
];
