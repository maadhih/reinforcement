<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Requests\UserRequest;
use Reinforcement\Acl\Transformer\UserTransformer;
use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(UserRequest $request, UserTransformer $transformer)
    {
        return $this->getPaginateResponse($request, $transformer);
    }

    public function show(UserRequest $request, UserTransformer $transformer, $id)
    {
        return $this->getModelResponse($id, $request, $transformer);
    }
}