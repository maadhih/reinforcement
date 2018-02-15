<?php

namespace Reinforcement\Database\Schema;

use Illuminate\Database\Schema\Blueprint AS IlluminateBlueprint;
use Illuminate\Support\Str;

class Blueprint extends IlluminateBlueprint
{
    /**
     * Specify a foreign key for the table.
     *
     * @param  string|array  $columns
     * @param  string  $name
     * @return \Illuminate\Support\Fluent
     */
    public function foreign($columns, $name = null)
    {
        $tableName = Str::plural(rtrim($columns, '_id'));
        return $this->indexCommand('foreign', $columns, $name)
                ->references('id')
                ->on($tableName)
                ->onUpdate('cascade')
                ->onDelete('restrict');
    }
}