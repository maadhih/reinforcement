<?php

namespace Reinforcement\Acl;

use Illuminate\Support\Facades\Route;

class Acl
{
    /**
     * Binds the Acl routes into the controller.
     *
     * @param  callable|null  $callback
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };
        $defaultOptions = [
            // 'prefix' => 'acl',
            'namespace' => '\Reinforcement\Acl\Controllers',
        ];
        $options = array_merge($defaultOptions, $options);
        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}