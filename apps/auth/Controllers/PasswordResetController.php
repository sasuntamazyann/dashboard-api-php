<?php

namespace Dashboard\DashboardApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\PasswordResetRequest\Service\PasswordResetRequestService;
use Dashboard\DashboardApi\Components\User\Repository\UserRepository;
use Dashboard\DashboardApi\Components\User\UserRole;
use Dashboard\DashboardApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;

class PasswordResetController extends BaseController
{
    private UserRepository $userRepository;
    private PasswordResetRequestService $passwordResetRequestService;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetRequestService $passwordResetRequestService,
    ) {
        $this->userRepository = $userRepository;
        $this->passwordResetRequestService = $passwordResetRequestService;
    }

    public function requestPasswordResetAction(ServerRequest $request, Response $response): Response
    {
        $email = $request->getParam('email');

        if (empty($email)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $user = $this->userRepository->findByEmailAndRole($email, UserRole::ROLE_ADMIN);
        if (is_null($user)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $this->passwordResetRequestService->requestPasswordReset($user);

        return $response->withJson([], 202);
    }

    public function resetPasswordAction(ServerRequest $request, Response $response): Response
    {
        $appName = $this->getAppHeaderValue($request);

        $passwordResetCode = $request->getParam('code');
        $newPassword = $request->getParam('new_password');

        if (empty($passwordResetCode) || empty($newPassword) || empty($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        if (false === $this->validateAppHeader($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        try {
            $this->passwordResetRequestService->resetUserPassword($passwordResetCode, $newPassword);
        } catch (RecordNotFoundException) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Password reset code not found',
            ];

            return $response->withJson($result, 404);
        }

        return $response->withJson([], 204);
    }
}
