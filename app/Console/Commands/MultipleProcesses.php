<?php

namespace App\Console\Commands;

use App\Models\ProductCl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MultipleProcesses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'khien:run {--channel=}';

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
}
