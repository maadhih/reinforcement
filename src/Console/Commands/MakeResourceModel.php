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
        $resources = (array) $resources;
        $module = $this->option('module');
        $migration = $this->option('migration');
        $newFields = $this->option('new-fields');
        $directory = app_path() .$ds. 'Models';

        $fillables = '';
        $relations = '';
        if ($migration) {
            $fieldCollection = $this->getFieldCollection($migration[0]);
            $fillables = $fieldCollection->getFieldsString();
            dd($fieldCollection->getFieldsMappedToValue('required'), $fillables);

            foreach ($fieldCollection->getRelations() as $relation) {
                $relations .= "public function $relation()\n{\nreturn \$this->belongsTo(".ucfirst($relation)."::class);\n}\n\n";
            }
        }

        $this->namespace = rtrim($this->namespace, "\\");


        foreach ($resources as $resource) {
            $filename = $directory . $ds . str_singular(ucfirst($resource)) . '.php';

            if ($newFields) {
                return $this->addNewFields($newFields, $filename);
            }

            $stub = file_get_contents($this->resourcesPath . $ds . 'Stubs' . $ds . (empty($module) ? 'Standard' : 'Modular') . $ds . 'Model.stub');
            $stub = str_replace(
                        ['{{namespace}}',  '{{resource}}', '{{fillables}}', '{{relations}}'],
                        [$this->namespace,  str_singular(ucfirst($resource)), $fillables, $relations],
                        $stub);

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            if (file_exists($filename)) {
                $this->info($filename . ' already exists!');
                return;
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

        $newfile = $this->insertAfter($file, $newString, 'protected $fillable = [');
        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }
}
