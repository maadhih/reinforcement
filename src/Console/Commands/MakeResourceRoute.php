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
     * Return the generated data
     *
     * @param  string  $resource
     * @return string
     */
    public function generate(string $resource)
    {
        $routesFile = $this->writeDirectory. DIRECTORY_SEPARATOR . 'web.php';

        $route = $this->makeRoute($resource);

        if (!file_exists($routesFile)) {
            $this->error('Routes file \'' . $routesFile . '\' does not exist!');
            return null;
        }

        $this->isUpdate = true;
        $routes = file_get_contents($routesFile);

        return $routes ? $routes . "\n" . $route : $route;
    }

    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName(string $resource)
    {
        return 'web';
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
