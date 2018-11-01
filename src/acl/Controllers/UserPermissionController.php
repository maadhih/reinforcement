<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Acl\Requests\UserPermissionRequest;
use Reinforcement\Acl\Transformer\PermissionTransformer;
use Reinforcement\Http\Controllers\Controller;

class UserPermissionController extends Controller
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(UserPermissionRequest $request, PermissionTransformer $transformer, $userId)
    {
        return $this->getPaginateResponse($request, $transformer, 'permissions', $userId);
    }

    public function show(UserPermissionRequest $request, PermissionTransformer $transformer, $userId, $roleId)
    {
        return $this->getModelResponse($userId, $request, $transformer, 'permissions', $roleId);
    }
}