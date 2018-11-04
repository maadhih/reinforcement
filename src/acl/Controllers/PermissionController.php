<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\PermissionRequest;
use Reinforcement\Acl\Repositories\PermissionRepository;
use Reinforcement\Http\Controllers\ResourceController;

class PermissionController extends ResourceController
{
    protected $repositoryClass = PermissionRepository::class;
    protected $requestClass = PermissionRequest::class;

}