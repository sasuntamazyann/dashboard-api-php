<?php

namespace Dashboard\DashboardApi\Components\UserInvitation;

enum UserInvitationStatus: string {
    case NOT_SENT = 'not_sent';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
}