<?php

namespace Dashboard\DashboardApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\User\Service\UserService;
use Dashboard\DashboardApi\Components\UserPreference\Service\UserPreferenceService;
use Dashboard\DashboardApi\Components\UserPreference\UserLanguage;
use Slim\Http\ServerRequest;

class UserController extends BaseController
{
    private const ERROR_INCORRECT_PASSWORD = 'wrong_password';

    private UserPreferenceService $userPreferenceService;
    private UserService $userService;

    public function __construct(
        UserPreferenceService $userPreferenceService,
        UserService $userService
    ) {
        $this->userPreferenceService = $userPreferenceService;
        $this->userService = $userService;
    }

    public function setLanguageAction(ServerRequest $request, Response $response): Response
    {
        $language = $request->getParam('language');
        if (!$language || is_null(UserLanguage::tryFrom($language))) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params'
            ];

            return $response->withJson($result, 400);
        }

        $authenticatedUser = $request->getAttribute('AuthUser');

        $this->userPreferenceService->setLanguagePreference($authenticatedUser->getId(), $language);

        return $response->withJson([], 204);
    }

    public function changePasswordAction(ServerRequest $request, Response $response): Response
    {
        $oldPassword = $request->getParam('old_password');
        $newPassword = $request->getParam('new_password');

        if (empty($oldPassword) || empty($newPassword)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $authenticatedUser = $request->getAttribute('AuthUser');

        $isPasswordChanged = $this->userService->changePassword($authenticatedUser, $oldPassword, $newPassword);
        if (!$isPasswordChanged) {
            $result = [
                'code' => self::ERROR_INCORRECT_PASSWORD,
                'message' => 'Incorrect current password provided.',
            ];

            return $response->withJson($result, 400);
        }

        return $response->withJson([], 204);
    }
}
