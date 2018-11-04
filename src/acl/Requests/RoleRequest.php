<?php

namespace Reinforcement\Acl\Requests;

use Reinforcement\Http\Request;

class RoleRequest extends Request
{
    protected $allowedIncludes = [
        'permissions'
    ];

    protected $allowedFilters = [
        'query'
    ];

    protected $allowedSorts = [
        'name',
        'slug',
    ];
}
