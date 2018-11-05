<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\User;
use Reinforcement\Repositories\Repository;
use Reinforcement\Acl\Repositories\UserRepositoryTrait;

/**
* User repository
*/
class UserRepository extends Repository
{
    protected $modelClass = User::class;

    use UserRepositoryTrait;

    public static function rolesFilteringMap()
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