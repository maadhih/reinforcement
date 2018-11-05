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
     * Register routes for ACL management.
     *
     * @return void
     */
    public function all()
    {
        $this->router->group(['middleware' => ['web'], 'prefix' => 'api'], function ($router) {
            $router->patch('users/{userId}/roles/attach', 'UserRoleController@attach')->name('users.roles.attach');
            $router->patch('users/{userId}/roles/{roleId}/detach', 'UserRoleController@detach')->name('users.roles.detach');

            $router->resource('users', 'UserController');
            $router->resource('roles', 'RoleController');
            $router->resource('permissions', 'PermissionController');

            $router->resource('users.roles', 'UserRoleController');
            $router->resource('users.permissions', 'UserPermissionController');
            $router->resource('roles.permissions', 'RolePermissionController');
        });
    }

}