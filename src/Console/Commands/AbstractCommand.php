<?php

namespace Reinforcement\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Reinforcement\Database\MigrationParser;
use Stringy\Stringy;

/**
* AbstractCommand
*/
class AbstractCommand extends Command
{
    protected $resourcesPath = '' ;
    protected $stubPath = '' ;
	protected $templatePath = '' ;

	protected $controllersPath = __DIR__ .'/../resources' ;
	protected $requestsPath = __DIR__ .'/../resources' ;
	protected $repositoriesPath = __DIR__ .'/../resources' ;
	protected $modelsPath = __DIR__ .'/../resources' ;

    public function __construct()
    {
        parent::__construct();

        $ds = DIRECTORY_SEPARATOR;
        $this->namespace = $this->getAppNamespace();

        $this->resourcesPath = __DIR__ ."$ds..$ds..$ds"."resources". $ds;
        $this->stubPath = $this->resourcesPath. "Stubs" .$ds;
        $this->templatePath = $this->resourcesPath. "Templates" .$ds;
    }

    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

    public function getFieldCollection($migrationClassName)
    {
    	$parser = new MigrationParser($migrationClassName, $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations');
        return $parser->getFieldCollection();
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
        $filePath = $this->writeDirectory . DIRECTORY_SEPARATOR . $filename . '.php';
        if (!file_exists($this->writeDirectory)) {
            mkdir($this->writeDirectory, 0777, true);
        }

        if (file_exists($filePath)) {
            $this->info($filePath . ' already exists!');
            // return false;
        }

        file_put_contents($filePath, $data);
        $this->info($filePath . ' created!');
        return $filePath;
    }
}