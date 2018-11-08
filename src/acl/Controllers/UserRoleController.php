<?php

namespace Reinforcement\Acl\Controllers;

use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Acl\Requests\UserRoleRequest;
use Reinforcement\Acl\Validators\UserRoleValidator;
use Reinforcement\Http\Controllers\ResourceController;

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
        $role = $user->roles()->getRelated()->findOrFail($roleId);

        $this->getRepository()->attachRoleWithPermissions($user, $role);
        return response($role, 201);
    }

    public function detachRole($id, $roleId)
    {
        $user = $this->getRepository()->getItem($id);
        $role = $user->roles()->getRelated()->findOrFail($roleId);

        $this->getRepository()->detachRoleWithPermissions($user, $role);
        return response('', 204);
    }
}