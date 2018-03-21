<?php

namespace Reinforcement\Database;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Filesystem\Filesystem;
use Reinforcement\Database\Schema\Blueprint;


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
    		$this->relations[] = camel_case(str_replace('_id', '', $parameters[0]));
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
            $this->relations[] = camel_case(str_replace('_id', '', $column));
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

    public function getFieldsString()
    {
    	return $this->arrayToFormattedString($this->fields);
    }

    public function getRelationsString()
    {
    	return $this->arrayToFormattedString($this->relations);
    }

    public function getRelationsStringSlug()
    {
        $foreignKeys =  array_map(function ($value)
        {
            return str_slug(str_replace('_id', '', $value));
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


    function encloseValuesInQuotes(array $array) {
        return array_map(function ($value)
        {
            return $this->encloseInQuotes($value);
        }, $array);
    }

    public function encloseInQuotes(string $string) {
        return "'".$string."'";
    }

    public function indent(string $string, int $level = 1, int $spaces = 4) {
        return str_repeat(" ", ($level * $spaces)).$string;
    }

    public function arrayToFormattedString($array) {
        if (empty($array)) return '';

        $array = $this->encloseValuesInQuotes($array);
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