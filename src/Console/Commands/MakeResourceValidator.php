<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Support\Str;

class MakeResourceValidator extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:validator {resources*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a validator that extends the Reinforcement validator';

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

        $this->writeDirectory = app_path('Validators');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Validator.stub';
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
        $newFields = $this->option('new-fields');

        $mappings = '';
        $validationRules = '';

        if ($migration) {
            $fieldCollection = $this->getFieldCollection($migration[0]);
            $mappings = $fieldCollection->getFieldsString(3);
            $validationRules = $fieldCollection->getFieldsMappedToValue('required', 3);
        }

        foreach ($resources as $resource) {
            // $filename = $directory . $ds . str_singular(ucfirst($resource)) . 'Validator.php';
            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }

            $validator = $this->makeValidator($this->namespace, $resource, $mappings, $validationRules);

            $this->writeFile(Str::singular(ucfirst($resource)) . 'Validator', $validator);
        }
    }

    public function makeValidator($namespace, $resource, $mappings, $rules)
    {
        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' =>  str_singular(ucfirst($resource)),
                'mappings' => $mappings,
                'validationRules' => $rules,
            ]);
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
