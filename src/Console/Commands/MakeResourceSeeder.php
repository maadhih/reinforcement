<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Database\FieldCollection;
use Reinforcement\Support\Str;

class MakeResourceSeeder extends AbstractCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:seeder {resources*} {--migration=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create seeder(s) that extends the Reinforcement seeder';

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

        $this->writeDirectory = base_path() .$ds. 'database' .$ds. 'seeds';
        $this->stub = $this->stubPath . 'Standard' . $ds . 'Seeder.stub';
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
        $attributes = '';

        if ($fieldCollection) {
            $attributes = $fieldCollection->getFieldsMappedToValue('value', 4);
        }

        $seeder = $this->makeSeeder($this->namespace, $resource, $attributes);

        $seederName = $this->getOutputFileName($resource);
        $this->updateDatabaseSeeder($seederName);

        return $seeder;
    }


    /**
     * Filename to save generated data
     * @param  string $resource
     * @return string
     */
    public function getOutputFileName(string $resource)
    {
        return Str::plural(ucfirst($resource)) . 'TableSeeder';
    }

    public function makeSeeder($namespace, $resource, $attributes)
    {
        return $this->buildFromStub($this->stub,
            [
                'namespace' => $namespace,
                'resource' => str_singular(ucfirst($resource)),
                'resourcePlural' =>  str_plural(ucfirst($resource)),
                'resourceLower' => str_singular(strtolower($resource)),
                'attributes' => $attributes,
            ]);
    }

    public function updateDatabaseSeeder($seederName)
    {
        $databaseSeeder = file_get_contents($this->writeDirectory."/DatabaseSeeder.php");
        $seederCall = "\n".Str::indent(2)."\$this->call($seederName::class);";

        $newfile = Str::insertAfterLast($databaseSeeder, $seederCall, ');');

        file_put_contents($this->writeDirectory."/DatabaseSeeder.php", $newfile);
    }
}
