<?php

namespace Dashboard\DashboardApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/coworkers', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\CoworkerController::class . ':listAction');
    $group->post('', Controllers\CoworkerController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\CoworkerController::class . ':deleteOneAction');
    $group->put('/{id}', Controllers\CoworkerController::class . ':updateOneAction');
    $group->post('/{id}/invite', Controllers\CoworkerController::class . ':inviteOneAction');
    $group->get('/search', Controllers\CoworkerController::class . ':searchAction');
});