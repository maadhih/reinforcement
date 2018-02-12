<?php

namespace Reinforcement\Console\Commands;


class FieldCollection
{
	protected $fields = [];
	protected $foreignKeys = [];
	protected $relations = [];
	protected $ignore = [
		'increments',
		'foreign',
		'index',
        'references',
        'on',
        'onUpdate',
        'onDelete',
        'default',
	];

	public function __call($method, $parameters)
    {
    	if(!in_array($method, $this->ignore) && !empty($parameters[0])){
	        $this->fields[] = $parameters[0];
    	}

    	if($method == 'foreign') {
    		$this->foreignKeys[] = $parameters[0];
    		$this->relations[] = camel_case(str_replace('_id', '', $parameters[0]));
    	}

        return $this;
    }

    public function getFields()
    {
    	return $this->fields;
    }

    public function getRelations()
    {
    	return $this->relations;
    }

    public function getFieldsString()
    {
    	$fields =  array_map(function ($value)
    	{
    		return "'". $value ."'";
    	}, $this->fields);
    	return implode(",\n", $fields);
    }

    public function getRelationsString()
    {
    	if (empty($this->relations))
    		return '';
    	$relations =  array_map(function ($value)
    	{
    		return "'". $value ."'";
    	}, $this->relations);
    	return implode(",\n", $relations);
    }

    public function getRelationsStringSlug()
    {
        if (empty($this->foreignKeys))
            return '';
        $foreignKeys =  array_map(function ($value)
        {
            return "'". str_slug(str_replace('_id', '', $value)) ."'";
        }, $this->foreignKeys);
        return implode(",\n", $foreignKeys);
    }

    public function getFieldsMapped()
    {
    	$fields = '';
    	foreach ($this->fields as $key => $value) {
    		$fields .= "'".$value."' => '" .$value."',\n";
    	}

    	return $fields;
    }

    public function getFieldsMappedToValue($mappingValue)
    {
    	$fields = '';
    	foreach ($this->fields as $key => $value) {
    		$fields .= "'".$value."' => '" .$mappingValue."',\n";
    	}

    	return $fields;
    }
}