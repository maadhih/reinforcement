<?php

namespace Reinforcement\Facades;

use Illuminate\Support\Facades\Facade;
use Reinforcement\Database\Schema\Blueprint;

class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function connection($name)
    {
        return static::$app['db']->connection($name)->getSchemaBuilder();
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected static function getFacadeAccessor()
    {
        $builder = static::$app['db']->connection()->getSchemaBuilder();
        $builder->blueprintResolver(function($table, $callback){
            return new Blueprint($table, $callback);
        });
        return $builder;
    }
}