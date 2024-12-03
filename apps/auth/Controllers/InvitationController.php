<?php

namespace Dashboard\DashboardApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\UserInvitation\Service\UserInvitationService;
use Slim\Http\ServerRequest;

class InvitationController extends BaseController
{
    private UserInvitationService $userInvitationService;

    public function __construct(UserInvitationService $userInvitationService)
    {
        $this->userInvitationService = $userInvitationService;
    }

    public function acceptInvitationAction(ServerRequest $request, Response $response): Response
    {
        $invitationAcceptCode = $request->getParam('code');
        $password = $request->getParam('password');

        if (empty($invitationAcceptCode) || empty($password)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $this->userInvitationService->acceptInvitation($invitationAcceptCode, $password);

        return $response->withJson([], 204);
    }
}