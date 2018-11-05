<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Acl\Requests\UserPermissionRequest;
use Reinforcement\Acl\Validators\UserPermissionValidator;
use Reinforcement\Http\Controllers\ResourceController;

class UserPermissionController extends ResourceController
{
    protected $repositoryClass = UserRepository::class;
    protected $requestClass = UserPermissionRequest::class;
    protected $validatorClass = UserPermissionValidator::class;
    protected $relation = 'permissions';
}