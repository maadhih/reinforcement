<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\UserPermission;
use Reinforcement\Repository\Repository;

/**
* User repository
*/
class UserPermissionRepository extends Repository
{
    protected $modelClass = UserPermission::class;
}