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
	protected $resourcesPath = __DIR__ .'/../../resources' ;

	protected $controllersPath = __DIR__ .'/../resources' ;
	protected $requestsPath = __DIR__ .'/../resources' ;
	protected $repositoriesPath = __DIR__ .'/../resources' ;
	protected $modelsPath = __DIR__ .'/../resources' ;

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

    protected function writeFile($filename, $data)
    {
        $filePath = $this->writeDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($this->writeDirectory)) {
            mkdir($this->writeDirectory, 0777, true);
        }

        if (file_exists($filePath)) {
            $this->info($filePath . ' already exists!');
            return;
        }

        return file_put_contents($filePath, $data);
    }
}