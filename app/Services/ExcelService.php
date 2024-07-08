<?php 

namespace App\Services;

use App\Jobs\ProcessExcelFileJob;

class ExcelService {
    public function getFileAndStartParcing($file) {
        $filePath = $file->store('excels');

        ProcessExcelFileJob::dispatch($filePath);
        
        $responseFilePath = str_replace('excels/', '', $filePath);

        return $responseFilePath;
    }
}