<?php

namespace Reinforcement\Database;

use Illuminate\Database\Seeder;

abstract class AbstractTableSeeder extends Seeder {
	protected $data;
	protected $className;

    public function __construct()
    {
        if (method_exists($this, 'getData'))
            $this->data = $this->getData();
    }

    public function run()
    {
		$inst = new $this->className;

        foreach ($this->data as $index => $record) {
            try {
                $item = $inst::firstOrNew(array('id' => $record['id']));

                foreach ($record as $key => $value) {
                    $item->$key = $value;
                }

                $item->save();

            } catch(Illuminate\Database\QueryException $excp) {
                dd($excp);
                echo "Not working." . PHP_EOL;
            }
        }
	}
}