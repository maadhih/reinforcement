<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\UserRequest;
use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Http\Controllers\ResourceController;

class UserController extends ResourceController
{
    protected $repositoryClass = UserRepository::class;
    protected $requestClass = UserRequest::class;
}