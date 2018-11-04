<?php

namespace Reinforcement\Acl\Requests;

use Reinforcement\Http\Request;

class RolePermissionRequest extends Request
{
    protected $allowedIncludes = [
    ];

    protected $allowedFilters = [
        'query'
    ];

    protected $allowedSorts = [
        'name',
        'slug',
    ];
}
