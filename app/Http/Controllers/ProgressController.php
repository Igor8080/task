<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ProgressController extends Controller
{
    public function __invoke($filePath)
    {
        $processedRows = Redis::get("parsing_progress:excels/{$filePath}");

        return response([
            'processed_rows' => $processedRows,
        ]);
    }
}
