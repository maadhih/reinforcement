<?php

namespace Reinforcement\Database\Schema;

use Illuminate\Database\Schema\Blueprint AS IlluminateBlueprint;
use Illuminate\Support\Facades\DB;
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

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @param  int  $precision
     * @return void
     */
    public function timestamps($precision = 0)
    {
        $this->timestamp('created_at', $precision)->default(DB::raw('CURRENT_TIMESTAMP'));

        $this->timestamp('updated_at', $precision)->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * Alias for illuminate default timestamps().
     *
     * @param  int  $precision
     * @return void
     */
    public function nullableTimestamps($precision = 0)
    {
        parent::timestamps($precision);
    }
}