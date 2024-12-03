<?php

namespace Dashboard\DashboardApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\Coworker\Repository\CoworkerRepository;
use Dashboard\DashboardApi\Components\Coworker\Service\CoworkerService;
use Dashboard\DashboardApi\Components\User\Repository\UserRepository;
use Dashboard\DashboardApi\Components\User\UserRole;
use Dashboard\DashboardApi\Components\UserInvitation\Service\UserInvitationService;
use Slim\Http\ServerRequest;

class CoworkerController extends BaseController
{
    private CoworkerService $coworkerService;

    private CoworkerRepository $coworkerRepository;

    private UserRepository $userRepository;

    private UserInvitationService $userInvitationService;

    public function __construct(
        CoworkerService $coworkerService,
        CoworkerRepository $coworkerRepository,
        UserRepository $userRepository,
        UserInvitationService $userInvitationService
    ) {
        $this->coworkerService = $coworkerService;
        $this->coworkerRepository = $coworkerRepository;
        $this->userRepository = $userRepository;
        $this->userInvitationService = $userInvitationService;
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $companyName = $request->getParam('company_name');
        $email = $request->getParam('email');

        if (empty($companyName) || empty($email)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $coworkerWithSameName = $this->coworkerRepository->findByCompanyName($companyName);
        if (false === is_null($coworkerWithSameName)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_NAME,
                'message' => 'Coworker with same name already exists',
            ];

            return $response->withJson($result, 400);
        }

        $coworkerWithSameEmail = $this->userRepository->findByEmailAndRole($email, UserRole::ROLE_COWORKER);
        if (false === is_null($coworkerWithSameEmail)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_EMAIL,
                'message' => 'Coworker with same email already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->coworkerService->createCoworker($companyName, $email);

        return $response->withJson([], 201);
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        $page = $request->getParam('page', self::DEFAULT_PAGE_VALUE);
        $perPage = $request->getParam('per_page', self::MAX_PER_PAGE);

        $response = $this->validatePagingParams($page, $perPage, $response);
        if (400 === $response->getStatusCode()) {
            return $response;
        }

        $offset = ($page - self::MIN_PAGE_VALUE) * $perPage;
        $coworkers = $this->coworkerRepository->getList($offset, $perPage);
        $coworkersTotalCount = $this->coworkerRepository->getTotalCount();

        $responseData = [];
        foreach ($coworkers as $coworker) {
            $responseData[] = [
                'id' => $coworker['id'],
                'company_name' => $coworker['companyName'],
                'registration_date' => $coworker['createdAt']->format('Y-m-d H:i:s'),
                'email' => $coworker['email'],
                'invitation_status' => $this->userInvitationService->getInvitationStatus($coworker['userId'])->value,
            ];
        }

        return $response->withJson(
            [
                'items' => $responseData,
                'total_count' => $coworkersTotalCount,
            ],
            200
        );
    }

    public function deleteOneAction(ServerRequest $request, Response $response)
    {
        $coworkerId = $request->getAttribute('id');
        $coworker = $this->coworkerRepository->findById($coworkerId);
        if (is_null($coworker)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Coworker with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        $this->coworkerService->deleteCoworker($coworker);

        return $response->withJson([], 204);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $newCompanyNewName = $request->getParam('company_name');
        $newEmail = $request->getParam('email');

        if (empty($newCompanyNewName) || empty($newEmail)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $coworkerId = $request->getAttribute('id');
        $coworker = $this->coworkerRepository->findById($coworkerId);
        if (is_null($coworker)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Coworker with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        if ($coworker->getCompanyName() !== $newCompanyNewName) {
            $coworkerWithSameName = $this->coworkerRepository->findByCompanyName($newCompanyNewName);
            if (false === is_null($coworkerWithSameName)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_NAME,
                    'message' => 'Coworker with same name already exists',
                ];

                return $response->withJson($result, 400);
            }
        }

        if ($coworker->getUser()->getEmail() !== $newEmail) {
            $coworkerWithSameEmail = $this->userRepository->findByEmailAndRole($newEmail, UserRole::ROLE_COWORKER);
            if (false === is_null($coworkerWithSameEmail)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_EMAIL,
                    'message' => 'Coworker with same email already exists',
                ];

                return $response->withJson($result, 400);
            }
        }

        $this->coworkerService->updateCoworker($coworker, $newCompanyNewName, $newEmail);

        return $response->withJson([], 204);
    }

    public function inviteOneAction(ServerRequest $request, Response $response): Response
    {
        $coworkerId = $request->getAttribute('id');
        $coworker = $this->coworkerRepository->findById($coworkerId);
        if (is_null($coworker)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Coworker with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        $this->coworkerService->sendInvitation($coworker);

        return $response->withJson([], 202);
    }

    public function searchAction(ServerRequest $request, Response $response): Response
    {
        $searchedName = $request->getParam('name');
        if (empty($searchedName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $coworkers = $this->coworkerRepository->searchByName($searchedName, 20);
        $searchData = [];
        foreach ($coworkers as $coworker) {
            $searchData[] = [
                'name' => $coworker['companyName'],
                'value' => $coworker['id'],
            ];
        }

        return $response->withJson(['items' => $searchData], 200);
    }
}
