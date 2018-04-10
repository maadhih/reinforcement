<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Database\FieldCollection;
use Reinforcement\Support\Str;

class MakeResourceRequest extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:request {resources*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a request that extends the Reinforcement request';

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

        $this->writeDirectory = app_path('Http' . $ds . 'Requests');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Request.stub';
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
        $fieldsString = $relationsStringSlug = '';

        if ($fieldCollection) {
            $fieldsString = $fieldCollection->getFieldsString(2);
            $relationsStringSlug = $fieldCollection->getRelationsStringSlug(2);
        }

        return $this->makeRequest($this->namespace, $resource, $fieldsString, $relationsStringSlug);
    }

    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName(string $resource)
    {
        return Str::singular(ucfirst($resource)) . 'Request';
    }

    public function makeRequest($namespace, $resource, $fields, $relations)
    {
        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' =>    Str::singular(ucfirst($resource)),
                'fieldsString' =>  $fields,
                'relationsString' =>   $relations
            ]);
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
