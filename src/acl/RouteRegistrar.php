<?php

namespace Reinforcement\Acl;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->z();
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function z()
    {
        $this->router->group(['middleware' => ['web'], 'prefix' => 'api'], function ($router) {
            $router->resource('users', 'UserController');
            $router->resource('roles', 'RoleController');
            $router->resource('permissions', 'PermissionController');

            $router->resource('users.roles', 'UserRoleController');
            $router->resource('users.permissions', 'UserPermissionController');
            $router->resource('roles.permissions', 'RolePermissionController');
        });
    }
}