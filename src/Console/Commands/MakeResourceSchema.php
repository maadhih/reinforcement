<?php

namespace Reinforcement\Console\Commands;

class MakeResourceSchema extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:resource:schema {resources*} {--module=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a schema that extends the Support schema';

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
        $directory = app_path('Schemas');

        if ($module) {
            $module = is_array($module) ? $module[0] : $module;
            $moduleDirectory = config('support.module.directory') . $ds . ucfirst($module);

            $this->namespace = $this->getAppNamespace() . ucfirst($module);
            $directory = config('support.module.directory') . $ds . ucfirst($module) . $ds . config('support.module.schemas');

            if (!file_exists($moduleDirectory)) {
                $this->error("The requested module '$moduleDirectory' does not exists!");
                return;
            }

            if(config('support.namespace')) {
                $this->namespace = config('support.namespace');
            }
        }

        foreach ($resources as $resource) {
            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Schema.stub');
            $stub = str_replace([
                            '{{namespace}}',
                            '{{resource}}',
                            '{{schemas}}',
                            '{{type}}',
                            '{{model}}'
                        ],
                        [
                            $this->namespace,
                            str_singular(ucfirst($resource)),
                            str_replace($ds, '\\', config('support.module.schemas')),
                            snake_case(str_plural($resource), '-'),
                            str_replace($ds, '\\', config('support.module.models'))
                        ], $stub);

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $filename = $directory . $ds . str_singular(ucfirst($resource)) . 'Schema.php';
            file_put_contents($filename, $stub);
            $this->info($filename . ' created!');
        }
    }
}
