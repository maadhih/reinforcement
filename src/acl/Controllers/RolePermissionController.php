<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Repositories\RoleRepository;
use Reinforcement\Acl\Requests\RolePermissionRequest;
use Reinforcement\Acl\Validators\RolePermissionValidator;
use Reinforcement\Http\Controllers\ResourceController;

class RolePermissionController extends ResourceController
{
    protected $repositoryClass = RoleRepository::class;
    protected $requestClass = RolePermissionRequest::class;
    protected $validatorClass = RolePermissionValidator::class;
    protected $relation ='permissions';
}