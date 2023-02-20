<?php

namespace App\Console\Commands;

use App\Models\ProductCl;
use App\Services\PThread;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MultipleProcesses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kira:run';
//    protected $signature = 'kira:run {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->handle3();
    }

    public function handle1()
    {
        $limit = 3;
        $channel = $this->option('channel');
        if ($channel == 2) {
            sleep(3);
        }


        $offset = ($channel - 1) * $limit;
        $cls = ProductCl::whereNull('pid')
            ->orderBy('id')
            ->offset($offset)
            ->limit($limit)->get();

        ProductCl::whereIn('id', $cls->pluck('id'))->update([
            'pid' => $channel
        ]);
        $ids = $cls->pluck('id')->implode("\n");
        Storage::disk('local')->append('example.txt', $ids);
        $message = date("Y-m-d h:i:sa") . "_ $channel";
        Log::debug($message);
        echo "done . " . $message;
    }

    public function handle2()
    {
        $start = microtime(true);
        /*------------------------------------------------------------------------------------------------------------*/
        $cls = ProductCl::whereNull('pid')
            ->orderBy('id')
            ->limit(10000)->get();

        $chunkItems = $cls->chunk(2000)->all();


        for ($i = 0; $i < 5; $i++) {
            $pid = pcntl_fork();

            if ($pid == -1) {
                exit("Error forking...\n");
            } else if ($pid == 0) {
                if (isset($chunkItems[$i])) {
                    $this->execute_task($i, $chunkItems[$i]);
                }
                exit();
            }
        }

// This while loop holds the parent process until all the child threads
// are complete - at which point the script continues to execute.
        while (pcntl_waitpid(0, $status) != -1) ;

// You could have more code here.
        echo "Do stuff after all parallel execution is complete.\n";
        $time = microtime(true) - $start;
        echo "Time = " . $time , " \n";
    }


    public function handle3()
    {
        // Creating the pool of threads(stored as array)
        $poolArr = array();
//Initiating the threads
        foreach (range("0", "3") as $i) {
            $poolArr[] = new PThread($i);
        }
//Start each Thread
        foreach ($poolArr as $t) {
            $t->start();
        }
//Wait all thread to finish
        foreach (range(0, 3) as $i) {
            $poolArr[$i]->join();
        }
//Next... other sentences with all threads finished.
    }

    /**
     * Helper method to execute a task.
     */
    public function execute_task($i, $items)
    {
        echo "Starting task: $i \n";
        foreach ($items as $item) {
            $item->update(['pid' => Str::random(111)]);
//            echo "$i _ $item->id . \n";
        }

        echo "______Completed task: ==> $i. \n";
    }

    public function test()
    {
        for ($x = 1; $x < 3; $x++) {
            switch ($pid = pcntl_fork()) {
                case -1:
                    // @fail
                    die('Fork failed');
                    break;

                case 0:
                    // @child: Include() misbehaving code here
                    print "FORK: Child #{$x} preparing to nuke...\n";
                    break;

                default:
                    // @parent
                    print "FORK: Parent, letting the child run amok...\n";
                    pcntl_waitpid($pid, $status);
                    break;
            }
        }
    }

}
