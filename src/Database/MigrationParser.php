<?php

namespace Reinforcement\Database;

use Illuminate\Filesystem\Filesystem;
use Reinforcement\Facades\Schema;


class MigrationParser
{
	protected $fieldCollection;

    public function __construct($migrationClassName, $migrationsPath)
    {
        $filesystem = app()->make(Filesystem::class);

        $migrationFileName = $filesystem->glob($migrationsPath.'/*'.snake_case($migrationClassName).'.php')[0];

        $filesystem->requireOnce($migrationFileName);
        $migrationClass = new $migrationClassName;
        $fieldCollection = new FieldCollection;

        Schema::setBlueprintResolver($fieldCollection);
        $migrationClass->up();

        $this->fieldCollection = $fieldCollection;
    }

    public function getFieldCollection()
    {
        return $this->fieldCollection;
    }
}