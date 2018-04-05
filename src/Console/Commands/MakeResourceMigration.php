<?php

namespace Reinforcement\Console\Commands;

class MakeResourceMigration extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:migration {resources*} {--module=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration(s)';

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
        $newFields = $this->option('new-fields');
        $directory = base_path() .$ds. 'database' .$ds. 'migrations';

            $this->namespace = rtrim($this->namespace, "\\");

        foreach ($resources as $resource) {
            if ($newFields) {
                return $this->addNewFields($newFields, $resource);
            }
            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Migration.stub');
            $stub = str_replace(
                    [
                        '{{namespace}}',
                        '{{resource}}',
                        '{{resourcePlural}}',
                        '{{resourcePluralLower}}',
                    ],
                    [
                        $this->namespace,
                        str_singular(ucfirst($resource)),
                        str_plural(ucfirst($resource)),
                        str_plural(snake_case($resource)),
                    ],$stub);

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $filename = $directory . $ds .date('Y_m_d_His').'_create_'.str_plural(snake_case($resource)) . '_table.php';

            if (file_exists($filename)) {
                $this->info($filename . ' already exists!');
                return;
            }

            file_put_contents($filename, $stub);
            $this->info($filename . ' created!');
        }
    }

    protected function addNewFields($newFields, $resource)
    {
        $migrationClass = new \ReflectionClass("Create".str_plural(ucfirst($resource))."Table");
        $filename = $migrationClass->getFileName();
        $file = file_get_contents($filename);

        $newString = '';
        foreach ($newFields as $field) {
            $newString = $newString ."\n\$table->string('$field');";
        }

        $newfile = $this->insertAfter($file, $newString, 'function (Blueprint $table) {');

        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }
}
