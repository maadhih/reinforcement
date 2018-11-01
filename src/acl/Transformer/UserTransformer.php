<?php

namespace Reinforcement\Acl\Transformer;

use Reinforcement\Acl\Models\User;
use Reinforcement\Acl\Transformer\RoleTransformer;
use Reinforcement\Transformer\Transformer;

class UserTransformer extends Transformer
{
    protected $resourceName = 'users';

    protected $availableIncludes = [
        'roles',
        'permissions'
    ];


    public function transform($user) : array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
        ];
    }


    /**
     * Include Role
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeRoles(User $user)
    {
        return $this->addCollectionRelation($user->roles, new RoleTransformer);
    }

    /**
     * Include Role
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includePermissions(User $user)
    {
        return $this->addCollectionRelation($user->permissions, new PermissionTransformer);
    }
}