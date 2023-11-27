<?php

namespace App\Console\Commands;

use App\Models\ProductCl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GetAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kira:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        $limit = 10;
        $lastId = 0;
        while(true) {
            $cls = ProductCl::whereNull('pid')
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit($limit)
                ->get();
            if (!$cls->all()) {
                break;
            }

            ProductCl::whereIn('id', $cls->pluck('id'))->update([
                'pid' => 1
            ]);
            $lastId = max($cls->pluck('id')->all());
            $ids = $cls->pluck('id')->implode("\n");
            Storage::disk('local')->append('example.txt', $lastId);
//            var_dump($cls->all()); die();
        }

    }
}
