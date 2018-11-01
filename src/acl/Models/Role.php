<?php

namespace Reinforcement\Acl\Models;

use Reinforcement\Acl\Models\Permission;
use Reinforcement\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_slug');
    }

    public static function mappings() : array
    {
        return [];
    }
}