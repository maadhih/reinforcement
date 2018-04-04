<?php

namespace Reinforcement\Console\Commands;

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
        $ds = DIRECTORY_SEPARATOR;

        $this->writeDirectory = app_path('Http' . $ds . 'Requests');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Request.stub';
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

        $fieldsString = '';
        $relationsStringSlug = '';

        if ($migration) {

            $fieldCollection = $this->getFieldCollection($migration[0]);
            $fieldsString = $fieldCollection->getFieldsString(2);
            $relationsStringSlug = $fieldCollection->getRelationsStringSlug(2);
        }

        foreach ($resources as $resource) {
            // $filename = $directory . $ds . str_singular(ucfirst($resource)) . 'Request.php';
            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }

            $request = $this->makeRequest($this->namespace, $resource, $fieldsString, $relationsStringSlug);

            $this->writeFile(Str::singular(ucfirst($resource)) . 'Request', $request);
        }
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
