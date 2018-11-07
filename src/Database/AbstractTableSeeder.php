<?php

namespace Reinforcement\Database;

use Illuminate\Database\Seeder;

abstract class AbstractTableSeeder extends Seeder {
    protected $data;
    protected $className;
    protected $update = true;

    public function __construct()
    {
        $this->modelInstance = new $this->className;
    }

    public function run()
    {

        $createdItems = $this->update ?  $this->updateSeed() : $this->freshSeed();

        $this->runPostCreateIfExist($createdItems);

    }

    public function getData()
    {
        return $this->data;
    }

    public function freshSeed()
    {
        if ($this->isTableEmpty()) {
            $this->modelInstance::insert($this->getData());
        }
        return null;
    }

    public function isTableEmpty()
    {
        return (bool) !$this->modelInstance::first();
    }

    public function setTimeStamps($data)
    {
        if (is_array_assoc($data)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            return $data;
        }

        return array_map(function($item) {
            return $this->setTimeStamps($item);

        }, $data);
    }


    public function updateSeed()
    {
        $createdItems = collect();

        foreach ($this->getData() as $index => $record) {
            $item = $this->modelInstance::firstOrNew(['id' => $record['id']]);

            foreach ($record as $key => $value) {
                $item->$key = $value;
            }

            $item->save();
            $createdItems->push($item);
        }

        return $createdItems;
    }


    public function runPostCreateIfExist($created)
    {
        if (!method_exists($this, 'postCreate')) {
            return false;
        }

        $this->postCreate($createdItems);
    }
}