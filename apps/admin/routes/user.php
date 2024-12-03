<?php

namespace Dashboard\DashboardApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/user', function (RouteCollectorProxy $group) {
    $group->post('/language', Controllers\UserController::class . ':setLanguageAction');
    $group->post('/change-password', Controllers\UserController::class . ':changePasswordAction');
});