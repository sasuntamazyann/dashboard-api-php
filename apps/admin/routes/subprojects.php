<?php

namespace Dashboard\DashboardApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/subprojects', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\SubprojectController::class . ':listAction');
    $group->post('', Controllers\SubprojectController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\SubprojectController::class . ':deleteOneAction');
    $group->put('/{id}', Controllers\SubprojectController::class . ':updateOneAction');
    $group->get('/search', Controllers\SubprojectController::class . ':searchAction');
});