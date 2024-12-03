<?php

namespace Dashboard\DashboardApi\Components\User;

enum UserStatus: string {
    case STATUS_ACTIVE = 'ACTIVE';
    case STATUS_INACTIVE = 'INACTIVE';
}