<?php

namespace Reinforcement\Acl\Requests;

use Reinforcement\Acl\PermissionChecker;
use Reinforcement\Http\Requests\JsonApiRequest;

class Request extends JsonApiRequest
{
    /**
     * Determine if the permission is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return PermissionChecker::check($this);
    }
}