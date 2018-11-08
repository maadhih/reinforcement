<?php

namespace Reinforcement\Acl\Repositories;

use DB;
use Reinforcement\Acl\Models\Role;
use Reinforcement\Acl\Models\UserInterface;

/**
* User repository
*/
trait UserRepositoryTrait
{
    public static function attachRoleWithPermissions(UserInterface $user, Role $role)
    {
        return DB::transaction(function () use ($user, $role) {
            $user->roles()->attach($role->id);
            $role->permissions->each(function($permission) use ($user, $role){
                $user->permissions()->attach($permission->slug, ['role_id' => $role->id]);
            });
        });
    }

    public static function detachRoleWithPermissions(UserInterface $user, Role $role)
    {
        return DB::transaction(function () use ($user, $role) {
            $user->roles()->detach($role->id);
            $role->permissions->each(function($permission) use ($user, $role){
                $user->permissions()->where('role_id', $role->id)->detach($permission->slug);
            });
        });
    }
}