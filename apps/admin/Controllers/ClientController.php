<?php

namespace Dashboard\DashboardApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Dashboard\DashboardApi\Components\Client\Repository\ClientRepository;
use Dashboard\DashboardApi\Components\Client\Service\ClientService;
use Slim\Http\ServerRequest;

class ClientController extends BaseController
{
    private ClientRepository $clientRepository;

    private ClientService $clientService;

    public function __construct(ClientRepository $clientRepository, ClientService $clientService)
    {
        $this->clientRepository = $clientRepository;
        $this->clientService = $clientService;
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $name = $request->getParam('name');

        if (empty($name)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $clientWithSameName = $this->clientRepository->findByName($name);
        if (false === is_null($clientWithSameName)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_NAME,
                'message' => 'Client with same name already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->clientService->createClient($name);

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
        $clients = $this->clientRepository->getList($offset, $perPage);
        $clientsTotalCount = $this->clientRepository->getTotalCount();

        $responseData = [];
        foreach ($clients as $client) {
            $responseData[] = [
                'id' => $client['id'],
                'name' => $client['name'],
                'registration_date' => $client['createdAt']->format('Y-m-d H:i:s'),
            ];
        }

        return $response->withJson(
            [
                'items' => $responseData,
                'total_count' => $clientsTotalCount,
            ],
            200
        );
    }

    public function deleteOneAction(ServerRequest $request, Response $response)
    {
        $clientId = $request->getAttribute('id');
        $client = $this->clientRepository->findById($clientId);
        if (is_null($client)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Client with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        $this->clientService->deleteClient($client);

        return $response->withJson([], 204);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $newName = $request->getParam('name');

        if (empty($newName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $clientId = $request->getAttribute('id');
        $client = $this->clientRepository->findById($clientId);
        if (is_null($client)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Client with supplied id could not be found',
            ];

            return $response->withJson($result, 404);
        }

        if ($client->getName() !== $newName) {
            $clientWithSameName = $this->clientRepository->findByName($newName);
            if (false === is_null($clientWithSameName)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_NAME,
                    'message' => 'Client with same name already exists',
                ];

                return $response->withJson($result, 400);
            }
        }

        $this->clientService->updateClient($client, $newName);

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

        $clients = $this->clientRepository->searchByName($searchedName, 20);
        $searchData = [];
        foreach ($clients as $client) {
            $searchData[] = [
                'name' => $client['name'],
                'value' => $client['id'],
            ];
        }

        return $response->withJson(['items' => $searchData], 200);
    }
}
