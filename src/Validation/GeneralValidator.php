<?php

namespace Reinforcement\Validation;

use Illuminate\Http\Request;

class GeneralValidator extends Validator
{
	public $mappings = [];

	public function createFromArray(array $params)
	{
		if(!empty($params['rules'])){
			$this->rules = $params['rules'];
		}

		if(!empty($params['mappings'])){
			$this->mappings = $params['mappings'];
		}

		if(!empty($params['messages'])){
			$this->messages = array_merge($this->messages, $params['messages']);
		}

		return $this;
	}

	public function rules(Request $request, array $params = [])
	{
		return $this->rules;
	}

	public function mappings()
	{
		return $this->mappings;
	}
}