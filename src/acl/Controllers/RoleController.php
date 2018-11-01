<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\RoleRequest;
use Reinforcement\Acl\Repositories\RoleRepository;
use Reinforcement\Acl\Transformer\RoleTransformer;
use Reinforcement\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct(RoleRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(RoleRequest $request, RoleTransformer $transformer)
    {
        return $this->getPaginateResponse($request, $transformer);
    }

    public function show(RoleRequest $request, RoleTransformer $transformer, $id)
    {
        return $this->getModelResponse($id, $request, $transformer);
    }

}