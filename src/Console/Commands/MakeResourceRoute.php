<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Support\Str;

class MakeResourceRoute extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:route {resources*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a resource route for a resource';

    protected $namespace;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->namespace = $this->getAppNamespace();
        parent::__construct();
        $ds = DIRECTORY_SEPARATOR;

        $this->writeDirectory = base_path('routes');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Route.stub';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ds = DIRECTORY_SEPARATOR;
        $resources = $this->argument('resources');
        $resources = (array) $resources;
        $routesFile = $this->writeDirectory. $ds . 'web.php';

        foreach ($resources as $resource) {
            $route = $this->makeRoute($resource);

            if (!file_exists($routesFile)) {
                $this->error('Routes file \'' . $routesFile . '\' does not exist!');
                return;
            }


            $routes = file_get_contents($routesFile);
            $routes = $routes ? $routes . "\n" . $route : $route;

            $this->writeFile('web', $routes, true);
        }
    }

    public function makeRoute($resource)
    {
        return $this->buildFromStub($this->stub,
            [
                'resource' =>    Str::plural(Str::slug(Str::snake($resource))),
                'controller' =>    Str::plural(ucfirst($resource)),
            ]);
    }
}
