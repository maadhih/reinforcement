<?php

namespace Reinforcement\Acl\Transformer;

use Reinforcement\Acl\Permission;

use Reinforcement\Transformer\Transformer;

class PermissionTransformer extends Transformer
{
    protected $resourceName = 'permissions';

    public function transform($permission) : array
    {
        return [
            'id' => $permission->slug,
            'name' => $permission->name
        ];
    }
}