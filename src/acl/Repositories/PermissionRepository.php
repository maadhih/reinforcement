<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\Permission;
use Reinforcement\Repositories\Repository;

/**
* Permission repository
*/
class PermissionRepository extends Repository
{
    protected $modelClass = Permission::class;

    public static function filteringMap()
    {
        return [

            'query' => [
                'name',
                'slug'
            ],
        ];
    }
}