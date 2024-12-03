<?php

namespace Dashboard\DashboardApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/projects', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\ProjectController::class . ':listAction');
    $group->post('', Controllers\ProjectController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\ProjectController::class . ':deleteOneAction');
    $group->put('/{id}', Controllers\ProjectController::class . ':updateOneAction');
    $group->get('/search', Controllers\ProjectController::class . ':searchAction');
    $group->patch('/{id}/publish', Controllers\ProjectController::class . ':publishOneAction');
    $group->patch('/{id}/unpublish', Controllers\ProjectController::class . ':unpublishOneAction');
});