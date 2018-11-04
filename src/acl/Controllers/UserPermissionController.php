<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Http\Controllers\ResourceController;
use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Acl\Requests\UserPermissionRequest;

class UserPermissionController extends ResourceController
{
    protected $repositoryClass = UserRepository::class;
    protected $requestClass = UserPermissionRequest::class;
    protected $validatorClass = UserPermissionValidator::class;
    protected $relation = 'permissions';
}