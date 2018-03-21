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

        $resolver = Blueprint::class;

        if (static::$app->isLocal() && !empty(session('blueprint-resolver'))) {
            $resolver = session('blueprint-resolver');
        }

        $builder->blueprintResolver(function($table, $callback = null) use ($resolver) {
            if (is_object($resolver)) {
               return $resolver->load($table, $callback);
            }
            return new $resolver($table, $callback);
        });
        return $builder;
    }

    public static function setBlueprintResolver($class)
    {
        session(['blueprint-resolver' => $class]);
    }
}