<?php

namespace Reinforcement\Console\Commands;

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
        $ds = DIRECTORY_SEPARATOR;
        $this->namespace = $this->getAppNamespace();
        parent::__construct();

        $this->writeDirectory = app_path('Repositories');
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Repository.stub';
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
        $fieldsMapped = '';
        if ($migration) {
            $fieldCollection = $this->getFieldCollection($migration[0]);
            $fieldsString = $fieldCollection->getFieldsString(4);
            $fieldsMapped = $fieldCollection->getFieldsMapped(3);
        }

        foreach ($resources as $resource) {
            // $filename = $directory . $ds . str_singular(ucfirst($resource)) . 'Repository.php';
            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }

            $repository = $this->makeRepository($this->namespace, $resource, $fieldsString, $fieldsMapped);

            $this->writeFile(Str::singular(ucfirst($resource)) . 'Repository', $repository);
        }
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
