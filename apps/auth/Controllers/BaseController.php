<?php

namespace Dashboard\DashboardApi\Auth\Controllers;

use Dashboard\DashboardApi\Components\User\UserRole;
use Slim\Http\ServerRequest;

abstract class BaseController
{
    protected const ERROR_INVALID_REQUEST_PARAMS = 'invalid_request_params';
    protected const ERROR_NOT_FOUND = 'not_found';
}