<?php

namespace Reinforcement\Acl\Transformer;

use Reinforcement\Acl\Models\Role;

use Reinforcement\Transformer\Transformer;

class RoleTransformer extends Transformer
{
    protected $resourceName = 'roles';

    public function transform($role) : array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
        ];
    }
}