<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCl;
use App\Services\PThread;
use Illuminate\Console\Command;
use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Support\Collection;


class Algolia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kira:algolia';

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
        echo "Starting................ \n";
        $start = microtime(true);
        /*------------------------------------------------------------------------------------------------------------*/
        $cls = ProductCl::whereNull('pid')
            ->orderBy('id')
            ->limit(5000)->get();

        $chunkItems = $cls->chunk(2500)->all();


        for ($i = 0; $i < 2; $i++) {
            $pid = pcntl_fork();

            if ($pid == -1) {
                exit("Error forking...\n");
            } else if ($pid == 0) {
                if (isset($chunkItems[$i])) {
                    $this->sync($i, $chunkItems[$i]);
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

    public function sync($i, Collection $items)
    {
        echo "Starting task: $i \n";
        $ids = $items->pluck('data')->all();
        $data = Product::whereIn('id', $ids)->get()->map(function ($item) {
            return array_merge($item->toArray(), ['objectID' => $item->id]);
        })->all();
//        dd($data);
        // Connect and authenticate with your Algolia app
        $client = SearchClient::create("key", "Fake");

// Create a new index and add a record
        $index = $client->initIndex("test_a");

        $index->saveObjects($data)->wait();
        echo "______Completed task: ==> $i. \n";
    }
}
