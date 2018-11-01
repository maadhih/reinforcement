<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\Role;
use Reinforcement\Repository\Repository;

/**
* Role repository
*/
class RoleRepository extends Repository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public static function getFiltering()
    {
        return [];
    }
}