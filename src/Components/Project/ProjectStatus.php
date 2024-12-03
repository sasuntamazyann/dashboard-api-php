<?php

namespace Dashboard\DashboardApi\Components\Project;

enum ProjectStatus: string {
    case STATUS_PUBLISHED = 'PUBLISHED';
    case STATUS_DRAFT = 'DRAFT';
}