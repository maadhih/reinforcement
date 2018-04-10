<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Database\FieldCollection;
use Reinforcement\Support\Str;

class MakeResourceRepository extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:repository {resources*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create repositor(y/ies) that extends the Reinforcement repository';

    protected $namespace;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $ds = DIRECTORY_SEPARATOR;

        $this->writeDirectory = app_path('Repositories');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Repository.stub';
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
        $fieldsString = $fieldsMapped = '';

        if ($fieldCollection) {
            $fieldsString = $fieldCollection->getFieldsString(4);
            $fieldsMapped = $fieldCollection->getFieldsMapped(3);
        }

        return $this->makeRepository($this->namespace, $resource, $fieldsString, $fieldsMapped);
    }

    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName(string $resource)
    {
        return Str::singular(ucfirst($resource)) . 'Repository';
    }

    public function makeRepository($namespace, $resource, $fields, $fieldsMapped)
    {
       return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' =>    Str::singular(ucfirst($resource)),
                'fieldsString' =>  $fields,
                'fieldsMapped' =>   $fieldsMapped
            ]);
    }

    protected function addNewFields($newFields, $filename)
    {
        $newString = '';
        $newStringMapped = '';
        foreach ($newFields as $field) {
            $newString = $newString ."'$field',";
            $newStringMapped = $newStringMapped ."\n'$field' => '$field',";
        }
        $file = file_get_contents($filename);

        $newfile = $this->insertAfter($file, $newString, "'query' => [");
        $newfile = $this->insertAfter($newfile, $newStringMapped, "public static function getFiltering()\n    {\n        return [");

        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }
}
