<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\Role;
use Reinforcement\Repository\Repository;

/**
* Role repository
*/
class RoleRepository extends Repository
{
    protected $modelClass = Role::class;

    public static function filteringMap()
    {
        return [
            'query' => [
                'name',
                'slug'
            ],
        ];
    }

    public static function permissionsFilteringMap()
    {
        return [
            'query' => [
                'name',
                'slug'
            ],
        ];
    }
}