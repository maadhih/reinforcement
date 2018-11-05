<?php

namespace Reinforcement\Acl\Validators;

use Illuminate\Http\Request;
use Reinforcement\Validation\Validator;

class RoleValidator extends Validator {

    public function rules(Request $request, array $params = [])
    {
        return [
            'name' => 'required',
            'slug' => 'required'
        ];
    }

    public function mappings()
    {
        return [
        ];
    }

    public function messages()
    {
        return [
            // Messages
        ];
    }
}