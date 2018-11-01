<?php

namespace Reinforcement\Acl\Middlewares;

use Closure;
use Illuminate\Http\Response;
use Reinforcement\Acl\PermissionChecker;

class Acl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next, $slug = null)
    {
        if(!PermissionChecker::check($request)) {
            return response()->json(['errors' => [
                [
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'details' => 'You are not authorized to access this resource.'
                ]
            ]], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}

