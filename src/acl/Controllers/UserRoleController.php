<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Http\Controllers\ResourceController;
use Reinforcement\Acl\Requests\UserRoleRequest;
use Reinforcement\Acl\Repositories\UserRepository;

class UserRoleController extends ResourceController
{
    protected $repositoryClass = UserRepository::class;
    protected $requestClass = UserRoleRequest::class;
    protected $validatorClass = UserRoleValidator::class;
    protected $relation = 'roles';

    public function attachRole($id)
    {
        $roleId = $this->validateRequest()['role_id'];

        $user = $this->getRepository()->getItem($id);
        $role = $this->getRepository($id, 'roles')->getItem($roleId);

        $this->getRepository()->attachRoleWithPermissions($user, $role);
        return response($role, 201);
    }

    public function detachRole($id, $roleId)
    {
        $user = $this->getRepository()->getItem($id);
        $role = $this->getRepository($id, 'roles')->getItem($roleId);

        $this->getRepository()->detachRoleWithPermissions($user, $role);
        return response('', 204);
    }
}