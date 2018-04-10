<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Database\FieldCollection;
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
     * Return the generated data
     *
     * @param  string                   $resource
     * @param  FieldCollection     $fieldCollection
     * @return string
     */
    public function generate(string $resource, FieldCollection $fieldCollection = null)
    {
        $mappings = $validationRules = '';

        if ($fieldCollection) {
            $mappings = $fieldCollection->getFieldsString(3);
            $validationRules = $fieldCollection->getFieldsMappedToValue('required', 3);
        }

        return $this->makeValidator($this->namespace, $resource, $mappings, $validationRules);
    }

    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName(string $resource)
    {
        return Str::singular(ucfirst($resource)) . 'Validator';
    }

    public function makeValidator($namespace, $resource, $mappings, $rules)
    {
        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' =>  Str::singular(ucfirst($resource)),
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
