<?php

namespace Reinforcement\Database;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Filesystem\Filesystem;
use Reinforcement\Database\Schema\Blueprint;
use Reinforcement\Support\Str;


class FieldCollection extends Blueprint
{
    protected $table;
	protected $fields = [];
	protected $foreignKeys = [];
	protected $relations = [];
	protected $ignore = [
        'id',
		'increments',
		'foreign',
		'index',
        'references',
        'on',
        'onUpdate',
        'onDelete',
        'default',
	];

    public function __construct()
    {
    }

    public function load($table, Closure $callback = null) {
        $this->table = $table;

        if (!empty($callback)) {
            $callback($this);
        }

        return $this;
    }


	public function __call($method, $parameters)
    {
        // dd($method);
    	if(!in_array($method, $this->ignore) && !empty($parameters[0])){
	        $this->fields[] = $parameters[0];
    	}

    	if($method == 'foreign') {
    		$this->foreignKeys[] = $parameters[0];
    		$this->relations[] = Str::camel(str_replace('_id', '', $parameters[0]));
    	}

        return $this;
    }

    public function addColumn($type, $name, array $parameters = [])
    {
        if(!in_array($name, $this->ignore)){
            $this->fields[] = $name;
        }
        return $this;
    }

    protected function addCommand($name, array $parameters = [])
    {
        if ($name == 'foreign') {
            $column = $parameters['columns'][0];
            $this->foreignKeys[] = $column;
            $this->relations[] = Str::camel(str_replace('_id', '', $column));
        }

        return $this;
    }


    public function getFields(){
        return $this->fields;
    }

    public function getRelations()
    {
    	return $this->relations;
    }

    public function getFieldsString($indent = 0)
    {
    	return $this->arrayToFormattedString($this->fields, $indent);
    }

    public function getRelationsString()
    {
    	return $this->arrayToFormattedString($this->relations);
    }

    public function getRelationsStringSlug()
    {
        $foreignKeys =  array_map(function ($value)
        {
            return Str::slug(str_replace('_id', '', $value));
        }, $this->foreignKeys);
        return $this->arrayToFormattedString($foreignKeys);
    }

    public function getFieldsMapped()
    {
        $fields = array_combine($this->fields, $this->fields);
        return $this->arrayAssocToFormattedString($fields);
    }

    public function getFieldsMappedToValue($mappingValue)
    {
    	$mapped = array_fill_keys($this->fields, $mappingValue);
    	return $this->arrayAssocToFormattedString($mapped);
    }


    function encloseValuesInQuotes(array $array, $indent = 0) {
        return array_map(function ($value) use ($indent)
        {
            return $this->encloseInQuotes($value, $indent);
        }, $array);
    }

    public function encloseInQuotes(string $string, $indent = 0) {
        return Str::indent($indent)."'".$string."'";
    }

    public function arrayToFormattedString($array, $indent = 0) {
        if (empty($array)) return '';

        $array = $this->encloseValuesInQuotes($array, $indent);
        return implode(",\n", $array);
    }

    public function arrayAssocToFormattedString($array) {
         $assoc = '';
        foreach ($array as $key => $value) {
            $assoc .= "'".$key."' => '" .$value."',\n";
        }

        return $assoc;
    }


    public function create() {
        return $this;
    }

    public function build(Connection $connection, Grammar $grammar)
    {
        return $this;
    }

    public function increments($column)
    {
        return $this;
    }

    public function timestamps($precision = 0)
    {
        return $this;
    }
}