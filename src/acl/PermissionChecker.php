<?php

namespace Reinforcement\Acl;

use Illuminate\Http\Request;


class PermissionChecker
{
    public static function check(Request $request)
    {
        if (\Config::get('acl.disabled')) {
            return true;
        }

        $user = $request->user();

        if (!$user) {
            return false;
        }

        return $user->hasPermission($request->route()->getName());
    }
}

