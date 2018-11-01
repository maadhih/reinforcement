<?php

namespace Reinforcement\Acl\Tests\Models;

use Reinforcement\Acl\Models\User as AclUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends AclUser implements AuthenticatableContract
{
    use Authenticatable;

    public function hasPermission(string $slug) : bool
    {
        if (\Config::get('acl.disabled')) {
            return true;
        }

        return (boolean) $this->permissions()->where('permissions.slug', $slug)->first();
    }
}