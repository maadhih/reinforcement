<?php

namespace Reinforcement;

use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Support\ServiceProvider;
use Reinforcement\Acl\Acl;
use Reinforcement\Acl\Commands\PermissionSync;
use Reinforcement\Acl\Models\UserInterface;
use Reinforcement\Http\Request;


class ReinforcementServiceProvider extends ServiceProvider
{
    private $commandPath = 'reinforcement.';

    private $packagePath = 'Reinforcement\Console\Commands\\';

    protected $commands = [
        \Reinforcement\Console\Commands\MakeAcl::class,
        \Reinforcement\Console\Commands\MakeResource::class,
        \Reinforcement\Console\Commands\MakeResourceMigration::class,
        \Reinforcement\Console\Commands\MakeResourceController::class,
        \Reinforcement\Console\Commands\MakeResourceRepository::class,
        \Reinforcement\Console\Commands\MakeResourceRequest::class,
        \Reinforcement\Console\Commands\MakeResourceRoute::class,
        \Reinforcement\Console\Commands\MakeResourceValidator::class,
        \Reinforcement\Console\Commands\MakeResourceModel::class,
        \Reinforcement\Console\Commands\MakeResourceSeeder::class,
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRequests();

        if (\Config::has('acl')) {
            Acl::routes();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->commands($this->commands);
        }

        if (\Config::has('acl')) {
            $this->configureACL();
        }
    }

    protected function configureRequests()
    {
        $this->app->resolving(Request::class, function ($request, $app)
        {
            // do not replace with $this->getRequest()
            // in tests when more than 1 request it will be executed more than once.
            // if replaced tests will fail
            $currentRequest = $app->make(IlluminateRequest::class);
            // $files          = $currentRequest->files->all();
            // $files          = is_array($files) === true ? array_filter($files) : $files;

            $request->initializeFromRequest($currentRequest);
            $this->app->instance(Request::class, $request);

            // $request->setUserResolver($currentRequest->getUserResolver());
            // $request->setRouteResolver($currentRequest->getRouteResolver());
            // $currentRequest->getSession() === null ?: $request->setSession($currentRequest->getSession());
            // $request->setJsonApiFactory($this->getFactory());
            // $request->setQueryParameters($this->getQueryParameters());
            // $request->setSchemaContainer($this->getSchemaContainer());
        });
    }

    protected function configureACL()
    {
        $this->commands([
            PermissionSync::class
        ]);

        $this->app->bind(UserInterface::class, \Config::get('acl.user'));
    }
}
