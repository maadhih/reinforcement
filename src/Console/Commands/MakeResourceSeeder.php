<?php

namespace Reinforcement\Console\Commands;

class MakeResourceSeeder extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:resource:seeder {resources*} {--module=*} {--migration=*}';

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
        $resources = !is_array($resources) ? [$resources] : $resources;
        $module = $this->option('module');
        $migration = $this->option('migration');
        $directory = base_path() .$ds. 'database' .$ds. 'seeds';

        $this->namespace = rtrim($this->namespace, "\\");

        $attributes = '';
        if ($migration) {
            $fieldCollection = $this->getFieldCollection('\\'.$migration[0]);
            $attributes = $fieldCollection->getFieldsMappedToValue('value');
        }

        foreach ($resources as $resource) {

            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Seeder.stub');
            $stub = str_replace(
                    [
                        '{{namespace}}',
                        '{{resource}}',
                        '{{resourcePlural}}',
                        '{{resourceLower}}',
                        '{{attributes}}',
                    ],
                    [
                        $this->namespace,
                        str_singular(ucfirst($resource)),
                        str_plural(ucfirst($resource)),
                        str_singular(strtolower($resource)),
                        $attributes
                    ],$stub);

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $filename = $directory . $ds . str_plural(ucfirst($resource)) . 'TableSeeder.php';

            if (file_exists($filename)) {
                $this->info($filename . ' already exists!');
                return;
            }

            file_put_contents($filename, $stub);
            $this->info($filename . ' created!');
        }
    }
}
