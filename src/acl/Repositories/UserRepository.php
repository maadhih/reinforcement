<?php

namespace Reinforcement\Acl\Repositories;

use Reinforcement\Acl\Models\Role;
use Reinforcement\Acl\Models\User;
use Reinforcement\Repository\Repository;
use DB;

/**
* User repository
*/
class UserRepository extends Repository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public static function getFiltering()
    {
        return [];
    }

    public static function addRoleWithPermissions(User $user, Role $role)
    {
        return DB::transaction(function () use ($user, $role) {
            $user->roles()->attach($role->slug);
            $role->permissions->each(function($permission) use ($user, $role){
                $user->permissions()->attach($permission->slug, ['role_id' => $role->id]);
            });
        });
    }

    public static function removeRoleWithPermissions(User $user, Role $role)
    {
        return DB::transaction(function () use ($user, $role) {
            $user->roles()->detach($role->slug);
            $role->permissions->each(function($permission) use ($user, $role){
                $user->permissions()->where('role_id', $role->id)->detach($permission->slug);
            });
        });
    }
}