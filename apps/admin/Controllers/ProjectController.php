<?php

namespace Dashboard\DashboardApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\Project\Repository\ProjectRepository;
use Dashboard\DashboardApi\Components\Project\Service\ProjectService;
use Slim\Http\ServerRequest;

class ProjectController extends BaseController
{
    private ProjectService $projectService;

    private ProjectRepository $projectRepository;

    public function __construct(
        ProjectService $projectService,
        ProjectRepository $projectRepository
    ) {
        $this->projectService = $projectService;
        $this->projectRepository = $projectRepository;
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
        $projects = $this->projectRepository->getList($offset, $perPage);
        $projectsTotalCount = $this->projectRepository->getTotalCount();

        $responseData = [];
        foreach ($projects as $project) {
            $responseData[] = [
                'id' => $project['id'],
                'code' => $project['code'],
                'name' => $project['name'],
                'status' => mb_strtolower($project['status']->value),
                'registration_date' => $project['createdAt']->format('Y-m-d H:i:s'),
                'client_name' => $project['clientName'],
                'client_id' => $project['clientId'],
                'coworker_name' => $project['coworkerName'],
                'coworker_id' => $project['coworkerId'],
            ];
        }

        return $response->withJson(
            [
                'items' => $responseData,
                'total_count' => $projectsTotalCount,
            ],
            200
        );
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $name = $request->getParam('name');
        $code = $request->getParam('code');
        $clientId = $request->getParam('client_id');
        $coworkerId = $request->getParam('coworker_id');

        if (empty($name) || empty($code) || empty($clientId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $projectWithSameName = $this->projectRepository->findByName($name);
        if (false === is_null($projectWithSameName)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_NAME,
                'message' => 'Project with same name already exists',
            ];

            return $response->withJson($result, 400);
        }

        $projectWithSameCode = $this->projectRepository->findByCode($code);
        if (false === is_null($projectWithSameCode)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_CODE,
                'message' => 'Project with same code already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->projectService->createProject($name, $code, $clientId, $coworkerId);

        return $response->withJson([], 201);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $newName = $request->getParam('name');
        $newCode = $request->getParam('code');
        $newClientId = $request->getParam('client_id');
        $newCoworkerId = $request->getParam('coworker_id');

        if (empty($newName) || empty($newCode) || empty($newClientId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $projectId = $request->getAttribute('id');
        $project = $this->projectRepository->findById($projectId);
        if (is_null($project)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Project with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        if ($project->getName() !== $newName) {
            $projectWithSameName = $this->projectRepository->findByName($newName);
            if (false === is_null($projectWithSameName)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_NAME,
                    'message' => 'Project with same name already exists',
                ];

                return $response->withJson($result, 400);
            }
        }

        if ($project->getCode() !== $newCode) {
            $projectWithSameCode = $this->projectRepository->findByCode($newCode);
            if (false === is_null($projectWithSameCode)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_CODE,
                    'message' => 'Project with same code already exists',
                ];

                return $response->withJson($result, 400);
            }
        }

        $this->projectService->updateProject($project, $newName, $newCode, $newClientId, $newCoworkerId);

        return $response->withJson([], 204);
    }

    public function deleteOneAction(ServerRequest $request, Response $response)
    {
        $projectId = $request->getAttribute('id');
        $project = $this->projectRepository->findById($projectId);
        if (is_null($project)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Project with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        $this->projectService->deleteProject($project);

        return $response->withJson([], 204);
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

        $projects = $this->projectRepository->searchByName($searchedName, 20);
        $searchData = [];
        foreach ($projects as $project) {
            $searchData[] = [
                'name' => $project['name'],
                'value' => $project['id'],
            ];
        }

        return $response->withJson(['items' => $searchData], 200);
    }

    public function publishOneAction(ServerRequest $request, Response $response): Response
    {
        $projectId = $request->getAttribute('id');
        $project = $this->projectRepository->findById($projectId);
        if (is_null($project)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Project with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->projectService->publishProject($project);

        return $response->withJson([], 204);
    }

    public function unpublishOneAction(ServerRequest $request, Response $response): Response
    {
        $projectId = $request->getAttribute('id');
        $project = $this->projectRepository->findById($projectId);
        if (is_null($project)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Project with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->projectService->unpublishProject($project);

        return $response->withJson([], 204);
    }
}