<?php

namespace Reinforcement\Console\Commands;

class MakeResourceModel extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:model {resources*} {--module=*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create model(s) that extends the Reinforcement model';

    protected $namespace;
    protected $writeDirectory;
    protected $stub;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->namespace = $this->getAppNamespace();
        $this->writeDirectory = app_path() .$ds. 'Models';
        $this->stub = $this->resourcesPath . $ds . 'Stubs' . $ds . 'Standard' . $ds . 'Model.stub';
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
        $resources = (array) $resources;
        $module = $this->option('module');
        $migration = $this->option('migration');
        $newFields = $this->option('new-fields');

        $fillables = '';
        $relations = '';
        if ($migration) {
            $fieldCollection = $this->getFieldCollection($migration[0]);
            $fillables = $fieldCollection->getFieldsString();
            dd($fieldCollection->getFieldsMappedToValue('required'), $fillables);

            $relations = $this->getRelationDefinitions($fieldCollection->getRelations());

        }

        $this->namespace = rtrim($this->namespace, "\\");


        foreach ($resources as $resource) {
            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }

            $model = $this->makeModel($this->namespace, $resource, $fillables, $relations);
            $this->writeFile(str_singular(ucfirst($resource)) . '.php', $model);

            $this->info($filename . ' created!');
        }
    }

    public function makeModel($namespace, $className, $fillables = '', $relationMethods ='') {
        $stub = file_get_contents($this->stub);
            $stub = str_replace(
                        [
                          '{{namespace}}',
                          '{{resource}}',
                          '{{fillables}}',
                          '{{relations}}'
                        ],
                        [
                            $namespace,
                            str_singular(ucfirst($className)),
                            $fillables,
                            $relationMethods
                        ], $stub);
    }

    protected function addNewFields($newFields, $filename)
    {
        $newString = '';
        foreach ($newFields as $field) {
            $newString = $newString ."\n'$field',";
        }
        $file = file_get_contents($filename);

        $newfile = $this->insertAfter($file, $newString, 'protected $fillable = [');
        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }

    protected function getRelationDefinitions (array $relations) {
        $definition = '';
        foreach ($relations as $relation) {
                $definition .= "public function $relation()\n{\nreturn \$this->belongsTo(".ucfirst($relation)."::class);\n}\n\n";
            }
    }
}
