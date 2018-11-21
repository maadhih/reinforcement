<?php

namespace Reinforcement\Validation;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Reinforcement\Support\Str;
use Reinforcement\Validation\Validator;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator as IlluminateValidation;

class ExtendedValidator extends IlluminateValidation
{

    /**
     * @param $attribute
     * @param array $ruleSet
     * @param array $messages
     * @throws \InvalidArgumentException
     */
    protected $ignoreId = null;

    public function iterate(Request $request, $attribute, Validator $validator, $messages = [])
    {
        $payload = $this->attributes();
        $input = array_get($payload, $attribute);
        if ((!is_null($input) && !is_array($input)) || empty($input)) {
            throw new \InvalidArgumentException('Attribute for iterate() must be an array.');
        }
        foreach ($input as $key => $value) {
            $this->addIteratedValidationRules($attribute.'.'.$key.'.', $validator->rules($request), $messages);
        }
    }


    /**
     * @param string $attribute
     * @param array $ruleSet
     * @param array $messages
     *
     * @return void
     */
    protected function addIteratedValidationRules($attribute, $ruleSet = [], $messages = [])
    {
        foreach ($ruleSet as $field => $rules) {
            $rules = str_replace('{parent}', rtrim($attribute, '.'), $rules);
            $rules = str_replace('{index}.', $attribute, $rules);
            $rules = is_string($rules) ? explode('|', $rules) : $rules;

            //If it contains nested iterated items, recursively add validation rules for them too
            if (isset($rules['iterate'])) {
                $this->iterateNestedRuleSet($attribute.$field, $rules);
                unset($rules['iterate']);
            }

            $this->rules = (new ValidationRuleParser($this->data))->mergeRules($this->rules, $attribute.$field, $rules);
        }
        $this->addIteratedValidationMessages($attribute, $messages);
    }

    /**
     * Add any custom messages for this ruleSet to the validator
     *
     * @param $attribute
     * @param array $messages
     *
     * @return void
     */
    protected function addIteratedValidationMessages($attribute, $messages = [])
    {
        foreach ($messages as $field => $message) {
            $field_name = $attribute.$field;
            $messages[$field_name] = $message;
        }
        $this->setCustomMessages($messages);
    }

    /**
     * @param $attribute
     * @param $rules
     *
     * @return void
     */
    protected function iterateNestedRuleSet($attribute, $rules)
    {
        $nestedRuleSet = isset($rules['iterate']['rules']) ? $rules['iterate']['rules'] : [];
        $nestedMessages = isset($rules['iterate']['messages']) ? $rules['iterate']['messages'] : [];
        $this->iterate($attribute, $nestedRuleSet, $nestedMessages);
    }

    public function validateRowExists($attributes, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'exists');

        $table = $parameters[0];

        // The second parameter position holds the name of the column that should be
        // verified as existing. If this parameter is not specified we will guess
        // that the columns being "verified" shares the given attribute's name.
        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        $expected = (is_array($value)) ? count($value) : 1;

        return $this->getRowExistCount($table, $column, $value, $parameters) >= $expected;
    }

    /**
     * Get the number of records that exist in storage.
     *
     * @param  string  $table
     * @param  string  $column
     * @param  mixed   $value
     * @param  array   $parameters
     * @return int
     */
    protected function getRowExistCount($table, $column, $value, $parameters)
    {
        $verifier = $this->getPresenceVerifier();
        $extra = $this->getExtraExistConditions($parameters);
        if (is_array($value)) {
            return $verifier->getMultiRowCount($table, $column, $value, $extra, $this->getData());
        } else {
            return $verifier->getRowCount($table, $column, $value, null, null, $extra, $this->getData());
        }
    }

    public function validateAlphaSpaces($attributes, $value, $parameters)
    {
        return preg_match('/^([a-z0-9_\-\s\,\.])+$/i', $value);
    }

    public function validateAlphaSpacesPercent($attributes, $value, $parameters)
    {
        return preg_match('/^([a-z0-9_\-\%\s\,\.])+$/i', $value);
    }

    public function validateAlphaSpaceApostrophe($attributes, $value, $parameters)
    {
        return preg_match("/^([a-z0-9_\-\%\s\,\.'])+$/i", $value);
    }

    public function validateAlphaSlashes($attributes, $value, $parameters)
    {
        return preg_match('/^([a-z0-9\-\s\/])+$/i', $value);
    }

    public function validateDateBetween($attributes, $value, $parameters)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        $date1 = \DateTime::createFromFormat('Y-m-d', $parameters[0]);
        $date2 = \DateTime::createFromFormat('Y-m-d', $parameters[1]);

        if ($date && $date1 && $date2) {
            return ($date1 < $date && $date < $date2);
        }
    }

    public function validateArrayMin($attribute, $value, $parameters)
    {
        if (! is_array($value)) {
            return;
        }

        return count($value) >= $parameters[0];
    }

    public function validateFileExtension($attribute, $value, $parameters)
    {
        $value = \Input::file($attribute);
        $this->data[$attribute] = $value;
        return ($value->getClientOriginalExtension() == $parameters[0]);
    }

    public function validateArrayExists($attribute, $array, $parameters)
    {
        if (! is_array($array)) {
            return;
        }
        foreach ($array as $key => $v) {
            if (is_array($v)) {
                if (!array_key_exists($parameters[0], $v)) {
                    return;
                }
                if ($v[$parameters[0]] == $parameters[1]) {
                    return true;
                }
            } else {
                if ($key == $parameters[0] && $v == $parameters[1]) {
                    return true;
                }
            }
        }

        return;
    }

    public function validateRowUnique($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'unique');

        $table = $parameters[0];

        // The second parameter position holds the name of the column that needs to
        // be verified as unique. If this parameter isn't specified we will just
        // assume that this column to be verified shares the attribute's name.
        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        list($idColumn, $id) = array(null, null);

        if (isset($parameters[2])) {
            list($idColumn, $id) = $this->getUniqueIds($parameters);

            if (strtolower($id) == 'null') {
                $id = null;
            }
        }

        $id = $this->ignoreId ?: $id;

        // The presence verifier is responsible for counting rows within this store
        // mechanism which might be a relational database or any other permanent
        // data store like Redis, etc. We will use it to determine uniqueness.
        $verifier = $this->getPresenceVerifier();

        $extra = $this->getUniqueExtra($parameters);

        return $verifier->getCount(

            $table, $column, $value, $id, $idColumn, $extra

        ) == 0;
    }


    protected function replaceArrayExists($message, $attribute, $rule, $parameters)
    {
        $message = "$attribute should have $parameters[0] key with value $parameters[1].";

        return $message;
    }

    public function replaceDateBetween($message, $attribute, $rule, $parameters)
    {
        return "This date should be between " . $parameters[0] . " and " . $parameters[1] . ".";
    }
    public function replaceHiisApiExists($message, $attribute, $rule, $parameters)
    {
        $input = $this->getData();
        return "There is no record of '" . $input[$attribute] . "' in $attribute.";
    }


    protected function replaceArrayMin($message, $attribute, $rule, $parameters)
    {
        return str_replace(':array_min', $parameters[0], $message);
    }

    public function setIgnoreId($id)
    {
        $this->ignoreId = (int) $id;
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function doReplacements($message, $attribute, $rule, $parameters)
    {
        $message = str_replace(':attribute', $this->extractAttribute($this->getAttribute($attribute)), $message);

        if (isset($this->replacers[Str::snake($rule)])) {
            $message = $this->callReplacer($message, $attribute, Str::snake($rule), $parameters);
        } elseif (method_exists($this, $replacer = "replace{$rule}")) {
            $message = $this->$replacer($message, $attribute, $rule, $parameters);
        }

        return $message;
    }


    protected function extractAttribute($attribute)
    {
        return str_replace('.attributes.', ' ',
                    str_replace(['.data.', 'id'], '',
                        str_replace(['data.attributes.', 'data.relationships.'], '', $attribute)));
    }
}