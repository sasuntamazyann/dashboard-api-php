<?php

namespace Dashboard\DashboardApi\Admin;

require_once __DIR__ . '/../../src/bootstrap.php';

use Dashboard\DashboardApi\Auth\Controllers\BaseController;
use Dashboard\DashboardApi\Integrations\Slim\Common\ApiErrorRenderer;
use DI\Bridge\Slim\Bridge;

$app = Bridge::create($container);

$app->add(
    new \Dashboard\DashboardApi\Integrations\Slim\Middleware\Authentication(
        $container->get(\Dashboard\DashboardApi\Authentication\AuthenticationService::class),
         BaseController::APP_NAME_ADMIN,
    ),
);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($_ENV['APP_DEBUG'] === "true", true, true);

// Error handler
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
//$errorHandler->setLogErrorRenderer($container->get(CustomLogRenderer::class));
$errorHandler->registerErrorRenderer('application/json', ApiErrorRenderer::class);

require_once 'routes/index.php';

// Run app
$app->run();