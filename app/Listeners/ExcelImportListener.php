<?php 

namespace App\Listeners;

use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Support\Facades\Log;

class ExcelImportListener
{
    public function handle(AfterImport $event)
    {
        Log::info('Import completed');
    }

    public function failed(ImportFailed $event)
    {
        Log::error('Import failed');
    }
}
