<?php

namespace App\Http\Controllers;

use App\Models\ProductCl;
use App\Services\MultipleProcesses;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Spatie\Async\Pool;
use Throwable;

class TestController extends BaseController
{
    public function index()
    {

    }
}
