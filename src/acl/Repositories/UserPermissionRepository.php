<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\UserPermission;
use Reinforcement\Repository\Repository;

/**
* User repository
*/
class UserPermissionRepository extends Repository
{
    public function __construct(UserPermission $model)
    {
        parent::__construct($model);
    }

    public static function getFiltering()
    {
        return [];
    }
}