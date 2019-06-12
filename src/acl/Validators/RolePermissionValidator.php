<?php

namespace Reinforcement\Acl\Validators;

use Illuminate\Http\Request;
use Reinforcement\Validation\Validator;

class RolePermissionValidator extends Validator {

    public function rules(Request $request, array $params = [])
    {
        return [
            'permission_id' => 'required',
        ];
    }

    public function mappings()
    {
        return [
            'permission_id' => 'permission_slug'
        ];
    }

    public function messages()
    {
        return [
            // Messages
        ];
    }
}