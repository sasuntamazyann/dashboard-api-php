<?php

namespace Dashboard\DashboardApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/clients', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\ClientController::class . ':listAction');
    $group->post('', Controllers\ClientController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\ClientController::class . ':deleteOneAction');
    $group->put('/{id}', Controllers\ClientController::class . ':updateOneAction');
    $group->get('/search', Controllers\ClientController::class . ':searchAction');
});