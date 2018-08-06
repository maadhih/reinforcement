<?php

namespace Reinforcement\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Reinforcement\Database\MigrationParser;
use Reinforcement\Support\Str;
use Stringy\Stringy;

/**
* AbstractCommand
*/
class AbstractCommand extends Command
{
    protected $resourcesPath = '';
    protected $stubPath = '';
    protected $templatePath = '';

    protected $resources = [];
    protected $fieldCollection;
	protected $isUpdate = false;


    public function __construct()
    {
        parent::__construct();

        $ds = DIRECTORY_SEPARATOR;
        $this->namespace = rtrim($this->getAppNamespace(), "\\");

        $this->resourcesPath = __DIR__ ."$ds..$ds..$ds"."resources". $ds;
        $this->stubPath = $this->resourcesPath. "Stubs" .$ds;
        $this->templatePath = $this->resourcesPath. "Templates" .$ds;
    }

    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

    public function handle()
    {
        $this->resources = (array) $this->argument('resources');
        // $newFields = $this->option('new-fields');

        foreach ($this->resources as $resource) {
            $fieldCollection = $this->getFieldCollection($resource);

            $fileData = $this->generate($resource, $fieldCollection);

            if (!empty($fileData)) {
                $this->writeFile($this->getOutputFileName($resource), $fileData);
            }
        }
    }

    /**
     * Get a FieldCollection instance for resource
     *
     * @param  string $resource
     * @return \Reinforcement\Database\FieldCollection|boolean
     */
    public function getFieldCollection(string $resource)
    {
        if ($this->hasOption('migration')) {

            $migrationClassName = '';
            $migration = $this->hasOption('migration') ? $this->option('migration') : '';

            if (empty($migration)) {
                $migrationClassName = 'Create'. Str::plural(ucfirst($resource)) .'Table';

            } else {
                $migrationClassName = $migration[0];
            }

        	$parser = new MigrationParser($migrationClassName, $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations');
            if ($parser) {
                return $parser->getFieldCollection();
            }
        }

        return null;
    }

    public function insertAfter($string, $insert, $after)
    {
        $string = Stringy::create($string);
        $firstHalf = $string->substr(0, $string->indexOf($after)+strlen($after));
        $secondHalf = $string->substr($string->indexOf($after)+strlen($after));

        return $firstHalf . $insert . $secondHalf;
    }

    public function buildFromStub($stub, array $replace) {
        $stub = file_get_contents($stub);

        $needles = array_map(function ($value)
        {
            return "{{" . $value . "}}";
        },
        array_keys($replace));

        return str_replace($needles, array_values($replace), $stub);
    }

    protected function writeFile($filename, $data)
    {
        $filePath = $this->writeDirectory . DIRECTORY_SEPARATOR . preg_replace('/\.php$/', '', $filename) . '.php';
        if (!file_exists($this->writeDirectory)) {
            mkdir($this->writeDirectory, 0777, true);
        }

        if (file_exists($filePath) && !$this->isUpdate) {
            $this->info($filePath . ' already exists!');
            return false;
        }

        file_put_contents($filePath, $data);
        $this->info($filePath . ($this->isUpdate ? ' updated!' : ' created!'));
        return $filePath;
    }
}