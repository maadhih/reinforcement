<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Support\Str;

class MakeResourceSeeder extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:seeder {resources*} {--migration=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create seeder(s) that extends the Support seeder';

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

        $this->writeDirectory = base_path() .$ds. 'database' .$ds. 'seeds';
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Seeder.stub';
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
        $migration = $this->option('migration');

        $this->namespace = rtrim($this->namespace, "\\");

        $attributes = '';
        if ($migration) {
            $fieldCollection = $this->getFieldCollection($migration[0]);
            $attributes = $fieldCollection->getFieldsMappedToValue('value', 4);
        }

        foreach ($resources as $resource) {

            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Seeder.stub');
            $seeder = $this->makeSeeder($this->namespace, $resource, $attributes);

            $this->writeFile(Str::plural(ucfirst($resource)) . 'TableSeeder', $seeder);
        }
    }

    public function makeSeeder($namespace, $resource, $attributes)
    {
        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' => str_singular(ucfirst($resource)),
                'resourcePlural' =>  str_plural(ucfirst($resource)),
                'resourceLower' => str_singular(strtolower($resource)),
                'attributes' => $attributes,
            ]);
    }
}
