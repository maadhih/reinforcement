<?php

namespace Reinforcement\Acl\Models;

use Reinforcement\Acl\Models\Role;
use Reinforcement\Database\Eloquent\Model;

class Permission extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'slug';

    protected $fillable = ['slug', 'name'];

    public static function mappings() : array
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}