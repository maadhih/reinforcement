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
     * Return the generated data
     *
     * @param  string                   $resource
     * @param  FieldCollection     $fieldCollection
     * @return string
     */
    public function generate(string $resource, FieldCollection $fieldCollection = null)
    {
        return $this->makeController($this->namespace, $resource);
    }


    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName($resource)
    {
        return Str::plural(ucfirst($resource)) . 'Controller';
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
