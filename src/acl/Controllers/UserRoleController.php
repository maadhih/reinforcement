<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\UserRoleRequest;
use Reinforcement\Acl\Transformer\RoleTransformer;
use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(UserRoleRequest $request, RoleTransformer $transformer, $userId)
    {
        return $this->getPaginateResponse($request, $transformer, 'roles', $userId);
    }

    public function show(UserRoleRequest $request, RoleTransformer $transformer, $userId, $roleId)
    {
        return $this->getModelResponse($userId, $request, $transformer, 'roles', $roleId);
    }
}