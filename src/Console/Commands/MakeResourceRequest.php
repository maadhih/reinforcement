<?php

namespace Reinforcement\Console\Commands;

class MakeResourceRequest extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:resource:request {resources*} {--module=*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a request that extends the Support request';

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
        $newFields = $this->option('new-fields');
        $directory = app_path('Http' . $ds . 'Requests');

        $fieldsString = '';
        $relationsStringSlug = '';

        if ($migration) {
            $fieldCollection = $this->getFieldCollection('\\'.$migration[0]);
            $fieldsString = $fieldCollection->getFieldsString();
            $relationsStringSlug = $fieldCollection->getRelationsStringSlug();
        }

        if ($module) {
            $module = is_array($module) ? $module[0] : $module;
            $moduleDirectory = config('support.module.directory') . $ds . ucfirst($module);

            $this->namespace = $this->getAppNamespace() . ucfirst($module);
            $directory = config('support.module.directory') . $ds . ucfirst($module) . $ds . config('support.module.requests');

            if (!file_exists($moduleDirectory)) {
                $this->error("The requested module '$moduleDirectory' does not exists!");
                return;
            }

            if(config('support.namespace')) {
                $this->namespace = config('support.namespace');
            }
        }

        foreach ($resources as $resource) {
            $filename = $directory . $ds . str_singular(ucfirst($resource)) . 'Request.php';
            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }

            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Request.stub');
            $stub = str_replace([
                            '{{namespace}}',
                            '{{validators}}',

                            '{{resource}}',
                            '{{fieldsString}}',
                            '{{relationsString}}',
                        ],
                        [
                            $this->namespace,
                            str_replace($ds, '\\', config('support.module.validators')),

                            str_singular(ucfirst($resource)),
                            $fieldsString,
                            $relationsStringSlug
                        ], $stub);

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            file_put_contents($filename, $stub);
            $this->info($filename . ' created!');
        }
    }

    protected function addNewFields($newFields, $filename)
    {
        $newString = '';
        foreach ($newFields as $field) {
            $newString = $newString ."\n'$field',";
        }
        $file = file_get_contents($filename);

        $newfile = $this->insertAfter($file, $newString, "protected function sortFieldParameters()\n    {\n        return [");
        $newfile = $this->insertAfter($newfile, $newString, "public function filteringParameters()\n    {\n        return [");

        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }
}
