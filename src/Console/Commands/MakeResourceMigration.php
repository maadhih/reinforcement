<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Support\Str;

class MakeResourceMigration extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:migration {resources*} {--module=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration(s)';

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

        $this->writeDirectory = base_path() . $ds . 'database' . $ds . 'migrations';
        $this->stub           = $this->stubPath . 'Standard' . $ds . 'Migration.stub';
    }


    /**
     * Return the generated data
     *
     * @param  string                   $resource
     * @param  FieldCollection     $fieldCollection
     * @return string
     */
    public function generate(string $resource)
    {
        return $this->makeMigration($this->namespace, $resource);
    }


    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName($resource)
    {
        return date('Y_m_d_His') . '_create_' . Str::plural(snake_case($resource)) . '_table';
    }

    public function makeMigration($namespace, $resource)
    {
        return $this->buildFromStub($this->stub,
            [
                'namespace'           => $this->namespace,
                'resource'            => str_singular(ucfirst($resource)),
                'resourcePlural'      => str_plural(ucfirst($resource)),
                'resourcePluralLower' => str_plural(snake_case($resource)),
            ]);
    }

    protected function addNewFields($newFields, $resource)
    {
        $migrationClass = new \ReflectionClass("Create" . str_plural(ucfirst($resource)) . "Table");
        $filename       = $migrationClass->getFileName();
        $file           = file_get_contents($filename);

        $newString = '';
        foreach ($newFields as $field) {
            $newString = $newString . "\n\$table->string('$field');";
        }

        $newfile = $this->insertAfter($file, $newString, 'function (Blueprint $table) {');

        file_put_contents($filename, $newfile);
        $this->info($filename . ' updated!');
        return true;
    }
}
