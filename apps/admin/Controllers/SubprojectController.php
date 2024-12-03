<?php

namespace Dashboard\DashboardApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\Subproject\Repository\SubprojectRepository;
use Dashboard\DashboardApi\Components\Subproject\Service\SubprojectService;
use Slim\Http\ServerRequest;

class SubprojectController extends BaseController
{
    private SubprojectRepository $subprojectRepository;

    private SubprojectService $subprojectService;

    public function __construct(
        SubprojectRepository $subprojectRepository,
        SubprojectService $subprojectService
    ) {
        $this->subprojectRepository = $subprojectRepository;
        $this->subprojectService = $subprojectService;
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        $projectId = $request->getParam('project_id');

        $page = $request->getParam('page', self::DEFAULT_PAGE_VALUE);
        $perPage = $request->getParam('per_page', self::MAX_PER_PAGE);

        if (!empty($projectId) && false === is_numeric($projectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $response = $this->validatePagingParams($page, $perPage, $response);
        if (400 === $response->getStatusCode()) {
            return $response;
        }

        $offset = ($page - self::MIN_PAGE_VALUE) * $perPage;
        $subprojects = $this->subprojectRepository->getList($projectId, $offset, $perPage);
        $subprojectsTotalCount = $this->subprojectRepository->getTotalCount($projectId);

        $responseData = [];
        foreach ($subprojects as $subproject) {
            $responseData[] = [
                'id' => $subproject['id'],
                'code' => $subproject['code'],
                'registration_date' => $subproject['createdAt']->format('Y-m-d H:i:s'),
                'coworker_name' => $subproject['coworkerName'],
                'coworker_id' => $subproject['coworkerId'],
                'project_name' => $subproject['projectName'],
                'project_id' => $subproject['projectId'],
            ];
        }

        return $response->withJson(
            [
                'items' => $responseData,
                'total_count' => $subprojectsTotalCount,
            ],
            200
        );
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $code = $request->getParam('code');
        $projectId = $request->getParam('project_id');
        $coworkerId = $request->getParam('coworker_id');

        if (empty($code) || empty($projectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $subProjectWithSameCode = $this->subprojectRepository->findByCodeAndProjectId($code, $projectId);
        if (false === is_null($subProjectWithSameCode)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_CODE,
                'message' => 'Subproject with same code already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->subprojectService->createSubproject($code, $projectId, $coworkerId);

        return $response->withJson([], 201);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $newCode = $request->getParam('code');
        $newProjectId = $request->getParam('project_id');
        $newCoworkerId = $request->getParam('coworker_id');

        if (empty($newCode) || empty($newProjectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $subprojectId = $request->getAttribute('id');
        $subproject = $this->subprojectRepository->findById($subprojectId);
        if (is_null($subproject)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Subproject with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        if ($subproject->getCode() !== $newCode) {
            $subProjectWithSameCode = $this->subprojectRepository->findByCodeAndProjectId($newCode, $newProjectId);
            if (false === is_null($subProjectWithSameCode)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_CODE,
                    'message' => 'Subproject with same code already exists.',
                ];

                return $response->withJson($result, 400);
            }
        }

        $this->subprojectService->updateSubproject($subproject, $newCode, $newProjectId, $newCoworkerId);

        return $response->withJson([], 204);
    }

    public function deleteOneAction(ServerRequest $request, Response $response)
    {
        $subprojectId = $request->getAttribute('id');
        $subproject = $this->subprojectRepository->findById($subprojectId);
        if (is_null($subproject)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Subproject with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->subprojectService->deleteSubproject($subproject);

        return $response->withJson([], 204);
    }

    public function searchAction(ServerRequest $request, Response $response): Response
    {
        $searchedProjectId = $request->getParam('project_id');
        if (empty($searchedProjectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $subprojects = $this->subprojectRepository->searchByProjectId($searchedProjectId);
        $searchData = [];
        foreach ($subprojects as $subproject) {
            $searchData[] = [
                'name' => $subproject['code'],
                'value' => $subproject['id'],
            ];
        }

        return $response->withJson(['items' => $searchData], 200);
    }
}
