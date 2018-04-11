<?php

namespace Reinforcement;

use Reinforcement\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request as IlluminateRequest;


class ReinforcementServiceProvider extends ServiceProvider
{
    private $commandPath = 'reinforcement.';

    private $packagePath = 'Reinforcement\Console\Commands\\';

    protected $commands = [
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
}
