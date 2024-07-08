<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RowsImport;
use Illuminate\Support\Facades\Redis;
use App\Models\Row;
use App\Events\RowCreated;

class ProcessExcelFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
       
        $import = new RowsImport();
        
        Excel::import($import, $this->filePath);
        
        Redis::set("parsing_progress:{$this->filePath}", $import->getProcessedRows());
        
        Row::created(function ($row) {
            event(new RowCreated($row));
        });

        return true;
    }
}

