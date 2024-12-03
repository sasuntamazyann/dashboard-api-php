<?php

namespace Dashboard\DashboardApi\Components\User;

enum UserRole: string {
    case ROLE_ADMIN = 'ADMIN';
    case ROLE_COWORKER = 'COWORKER';
}