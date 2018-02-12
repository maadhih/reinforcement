<?php

namespace Reinforcement\Console\Commands;

use Reinforcement\Console\Commands\FieldCollection;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
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
    	$migrationClass = new \ReflectionClass($migrationClassName);

        $migrationFile = file_get_contents($migrationClass->getFileName());

        eval('$a= function ('. Stringy::create($migrationFile)->stripWhitespace()->between("function(", '}')->replace('Blueprint', FieldCollection::class) .'return $table;};');
        $fieldCollection = $a(new FieldCollection);
        return $fieldCollection;
    }

    public function insertAfter($string, $insert, $after)
    {
        $string = Stringy::create($string);
        $firstHalf = $string->substr(0, $string->indexOf($after)+strlen($after));
        $secondHalf = $string->substr($string->indexOf($after)+strlen($after));

        return $firstHalf . $insert . $secondHalf;
    }
}