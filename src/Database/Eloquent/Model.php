<?php

namespace Reinforcement\Database\Eloquent;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model AS EloquentModel;


/**
* Base model for extending eloquent based function
*/
abstract class Model extends EloquentModel implements ModelInterface
{
    protected $generated = [];

    public static function mappings() {
        return [];
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->setGeneratedAttributes();
    //     });

    // }

    // public function setGeneratedAttributes()
    // {
    //     foreach ($this->generated as $attribute => $generator) {
    //        $this->setAttribute($attribute, NumberGenerator::$generator($this));
    //     }
    //     return $this;
    // }


    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */

    public function __call($method, $parameters)
    {
        if (strpos($method, '-') !== false) {
            $method = Str::camel($method);
            return $this->$method(...$parameters);
        }

        return parent::__call($method, $parameters);
    }


}
