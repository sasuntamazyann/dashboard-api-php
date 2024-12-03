<?php

namespace Dashboard\DashboardApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Authentication\AuthenticationService;
use Dashboard\DashboardApi\Components\User\Repository\UserRepository;
use Dashboard\DashboardApi\Components\User\UserRole;
use Slim\Http\ServerRequest;

class AuthenticationController extends BaseController
{
    private const ERROR_INCORRECT_CREDENTIALS = 'incorrect_credentials';

    private UserRepository $userRepository;
    private AuthenticationService $authenticationService;

    public function __construct(UserRepository $userRepository, AuthenticationService $authenticationService)
    {
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    public function authenticateAction(ServerRequest $request, Response $response): Response
    {
        $email = $request->getParam('email');
        $password = $request->getParam('password');

        if (empty($email) || empty($password)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $user = $this->userRepository->findByEmailAndRole($email, UserRole::ROLE_ADMIN);
        if (is_null($user)) {
            $result = [
                'code' => self::ERROR_INCORRECT_CREDENTIALS,
                'message' => 'Incorrect email password'
            ];

            return $response->withJson($result, 401);
        }

        if (!$this->authenticationService->verifyUserPassword($user->getPassword(), $password)) {
            $result = [
                'code' => self::ERROR_INCORRECT_CREDENTIALS,
                'message' => 'Incorrect email password'
            ];

            return $response->withJson($result, 401);
        }

        $accessToken = $this->authenticationService->generateAccessToken($user);
        $response = $response->withHeader('Authorization', 'Bearer ' . $accessToken);

        return $response->withJson([], 204);
    }
}
