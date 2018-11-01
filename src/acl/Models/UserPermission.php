<?php

namespace Reinforcement\Acl\Models;

use Reinforcement\Acl\Models\Model;
use Reinforcement\Acl\Models\Role;

class UserPermission extends Model
{
    protected $table  = 'user_permission';

    protected $guarded = [];

    public static function mappings() : array
    {
        return [];
    }
}