<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\Permission;
use Reinforcement\Repository\Repository;

/**
* Permission repository
*/
class PermissionRepository extends Repository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public static function getFiltering()
    {
        return [];
    }
}