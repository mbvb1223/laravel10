<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Async\Task;

class MultipleProcesses extends Task
{
    private $i;
    private $items;

    public function __construct($i, $items)
    {
        $this->i = $i;
        $this->items = $items;
    }

    public function configure()
    {

    }

    public function run()
    {
        return $message = date("Y-m-d h:i:sa") . "_ $this->i";
//        Log::debug($message);

//        $this->execute_task($this->i, $this->items);
    }

    private function execute_task($i, mixed $items)
    {
        echo "Starting task: $i \n";
        foreach ($items as $item) {
            $item->update(['pid' => Str::random(111)]);
        }

        echo "______Completed task: ==> $i. \n";
    }
}
