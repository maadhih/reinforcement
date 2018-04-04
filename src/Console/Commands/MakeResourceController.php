<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Support\Str;

class MakeResourceController extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:controller {resources*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create controller(s) that extends the Reinforcement controller';

    protected $namespace;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $ds = DIRECTORY_SEPARATOR;

        $this->writeDirectory = app_path('Http' . $ds . 'Controllers');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Controller.stub';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $resources = (array) $this->argument('resources');

        foreach ($resources as $resource) {

            $controller = $this->makeController($this->namespace, $resource);

            $this->writeFile(Str::plural(ucfirst($resource)) . 'Controller', $controller);
        }
    }

    public function makeController($namespace, $resource) {

        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' => Str::singular(ucfirst($resource)),
                'className' => Str::plural(ucfirst($resource)),
            ]);
    }
}
