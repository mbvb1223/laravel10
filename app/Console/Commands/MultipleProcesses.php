<?php

namespace App\Console\Commands;

use App\Models\ProductCl;
use App\Services\PThread;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Async\Pool;
use Symfony\Component\Process\Process;
use Throwable;

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
        echo "Time = " . $time, " \n";
    }

    public function handle4()
    {
        for ($i = 0; $i < 5; $i++) {
            $process = new Process('php ' . base_path('artisan') . " task {$i}");
            $process->setTimeout(0);
            $process->disableOutput();
            $process->start();
            $processes[] = $process;
        }
    }

    public function handle5()
    {
        $cls = ProductCl::whereNull('pid')
            ->orderBy('id')
            ->limit(10)->get();

        $chunkItems = $cls->chunk(5)->all();

        $pool = Pool::create();

        foreach ($chunkItems as $key => $items) {
            $all = $items->all();
            $pool->add(function () use ($key, $all) {
                foreach ($all as $item) {
                    $item = ProductCl::find($item['id']);
                    $item->update(['pid' => Str::random(111)]);
                }

                $file_name = "khien_$key.txt";
                $content = bin2hex(random_bytes(2048));
                file_put_contents($file_name, $content);
                return $file_name;
            })
                ->then(function ($output) {
                    echo "Generated file: $output" . PHP_EOL;
                })
                ->catch(function (Throwable $exception) {
                    echo "$exception";
                });
        }

        $pool->wait();
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
