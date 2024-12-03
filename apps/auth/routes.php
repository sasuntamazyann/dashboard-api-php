<?php

namespace Dashboard\DashboardApi\Auth;

global $app;

$app->post('/auth', Controllers\AuthenticationController::class . ':authenticateAction');
$app->post('/request-password-reset', Controllers\PasswordResetController::class . ':requestPasswordResetAction');
$app->post('/reset-password', Controllers\PasswordResetController::class . ':resetPasswordAction');
$app->post('/accept-invite', Controllers\InvitationController::class . ':acceptInvitationAction');
