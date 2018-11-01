<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\PermissionRequest;
use Reinforcement\Acl\Repositories\PermissionRepository;
use Reinforcement\Acl\Transformer\PermissionTransformer;
use Reinforcement\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function __construct(PermissionRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(PermissionRequest $request, PermissionTransformer $transformer)
    {
        return $this->getPaginateResponse($request, $transformer);
    }

    public function show(PermissionRequest $request, PermissionTransformer $transformer, $id)
    {
        return $this->getModelResponse($id, $request, $transformer);
    }

}