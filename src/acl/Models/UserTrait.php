<?php

namespace Reinforcement\Acl\Models;

trait UserTrait
{

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_slug');
    }

    public function hasPermission(string $slug) : bool
    {
        return (boolean) $this->permissions()->where('permissions.slug', $slug)->first();
    }
}