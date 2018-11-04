<?php

namespace Reinforcement\Acl\Controllers;

use App\Validators\RoleValidator;
use Reinforcement\Acl\Requests\RoleRequest;
use Reinforcement\Http\Controllers\ResourceController;
use Reinforcement\Acl\Repositories\RoleRepository;

class RoleController extends ResourceController
{
    protected $repositoryClass = RoleRepository::class;
    protected $requestClass = RoleRequest::class;
    protected $validatorClass = RoleValidator::class;

}