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
		$instance = new $this->className;
        $createdItems = collect();

        foreach ($this->data as $index => $record) {
            try {
                $item = $instance::firstOrNew(['id' => $record['id']]);

                foreach ($record as $key => $value) {
                    $item->$key = $value;
                }

                $item->save();
                $createdItems->push($item);

            } catch(Illuminate\Database\QueryException $excp) {
                dd($excp);
                echo "Not working." . PHP_EOL;
            }
        }

        if (method_exists($this, 'postCreate')) {
            $this->postCreate($createdItems);
        }

	}
}