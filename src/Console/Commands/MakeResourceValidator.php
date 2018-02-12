<?php

namespace Reinforcement\Console\Commands;

class MakeResourceValidator extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:resource:validator {resources*} {--module=*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a validator that extends the Support abstract validator';

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
        $directory = app_path('Validators');

        $mappings = '';
        $validationRules = '';
        if ($migration) {
            $fieldCollection = $this->getFieldCollection('\\'.$migration[0]);
            $mappings = $fieldCollection->getFieldsMapped();
            $validationRules = $fieldCollection->getFieldsMappedToValue('required');
        }

        if ($module) {
            $module = is_array($module) ? $module[0] : $module;
            $moduleDirectory = config('support.module.directory') . $ds . ucfirst($module);

            $this->namespace = $this->getAppNamespace() . ucfirst($module);
            $directory = config('support.module.directory') . $ds . ucfirst($module) . $ds . config('support.module.validators');

            if (!file_exists($moduleDirectory)) {
                $this->error("The requested module '$moduleDirectory' does not exists!");
                return;
            }

            if(config('support.namespace')) {
                $this->namespace = config('support.namespace');
            }
        }

        foreach ($resources as $resource) {
            $filename = $directory . $ds . str_singular(ucfirst($resource)) . 'Validator.php';
            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }
            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Validator.stub');
            $stub = str_replace([
                            '{{namespace}}',
                            '{{validators}}',
                            '{{resource}}',
                            '{{mappings}}',
                            '{{validationRules}}',
                        ],
                        [
                            $this->namespace,
                            str_replace($ds, '\\', config('support.module.validators')),
                            str_singular(ucfirst($resource)),
                            $mappings,
                            $validationRules,
                        ],
                        $stub);

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
        $newStringMapped = '';
        foreach ($newFields as $field) {
            $newString = $newString ."\n'$field' => 'required',";
            $newStringMapped = $newStringMapped ."\n'$field' => '$field',";
        }
        $file = file_get_contents($filename);

        $newfile = $this->insertAfter($file, $newString, "public function rules(Request \$request, array \$params = array())\n    {\n        return [");
        $newfile = $this->insertAfter($newfile, $newStringMapped, "public function mappings()\n    {\n        return [");

        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }
}
