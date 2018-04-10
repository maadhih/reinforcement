<?php

namespace Reinforcement\Database;

use Illuminate\Filesystem\Filesystem;
use Reinforcement\Facades\Schema;
use Reinforcement\Support\Str;


class MigrationParser
{
	protected $fieldCollection;

    public function __construct($migrationClassName, $migrationsPath)
    {
        $filesystem = app()->make(Filesystem::class);

        $fileList = $filesystem->glob($migrationsPath.'/*'.Str::snake($migrationClassName).'.php');
        if (empty($fileList)){
            return false;
        }

        $migrationFileName = $fileList[0];

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