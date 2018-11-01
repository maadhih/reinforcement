<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\RolePermissionRequest;
use Reinforcement\Acl\Transformer\PermissionTransformer;
use Reinforcement\Acl\Repositories\RoleRepository;
use Reinforcement\Http\Controllers\Controller;

class RolePermissionController extends Controller
{
    public function __construct(RoleRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(RolePermissionRequest $request, PermissionTransformer $transformer, $roleId)
    {
        return $this->getPaginateResponse($request, $transformer, 'permissions', $roleId);
    }

    public function show(RolePermissionRequest $request, PermissionTransformer $transformer, $roleId, $permissionId)
    {
        return $this->getModelResponse($roleId, $request, $transformer, 'permissions', $permissionId);
    }

}