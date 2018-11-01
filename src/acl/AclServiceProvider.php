<?php

namespace Reinforcement\Acl;

// use Reinforcement\Acl\Acl;
use Reinforcement\Acl\Commands\PermissionSync;
use Reinforcement\Acl\Models\UserInterface;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class AclServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Reinforcement\Acl\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // php artisan vendor:publish --provider="Reinforcement\Acl\AclServiceProvider" --tag=config --tag=migrations

        $this->publishes([
            __DIR__.'/config/acl.php' => config_path('acl.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');

        Acl::routes();
    }

    public function register()
    {
        $this->commands([
            PermissionSync::class
        ]);

        $this->registerAclUser();
    }

    public function registerAclUser()
    {
        $this->app->bind(UserInterface::class, \Config::get('acl.user'));
    }
}