<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Database\FieldCollection;
use Reinforcement\Support\Str;

class MakeResourceModel extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:model {resources*} {--migration=*} {--new-fields=*}';

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
        parent::__construct();
        $ds = DIRECTORY_SEPARATOR;

        $this->writeDirectory = app_path() .$ds. 'Models';
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Model.stub';
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
        $fillables = $relations = '';

        if ($fieldCollection) {

            $fillables = $fieldCollection->getFieldsString(2);
            $relations = $this->getRelationDefinitions($fieldCollection->getRelations());
        }

        // if ($newFields) {
        //     return $this->addNewFields($newFields, $filename);
        // }

        return $this->makeModel($this->namespace, $resource, $fillables, $relations);
    }


    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName(string $resource)
    {
        return Str::singular(ucfirst($resource));
    }

    public function makeModel($namespace, $resource, $fillables = '', $relationMethods ='') {

        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'className' => str_singular(ucfirst($resource)),
                'fillables' => $fillables,
                'relations' => $relationMethods
            ]);
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

    protected function getRelationDefinitions (array $relations)
    {
        $definitions = '';
        foreach ($relations as $relation) {
            $definitions .= $this->buildFromStub($this->templatePath . 'RelationMethod.stub',
            [
                'relation' => $relation,
                'belongsTo' => ucfirst($relation)
            ]);
        }

        return $definitions;
    }
}
