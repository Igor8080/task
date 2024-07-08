<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExcelRequest;
use App\Services\ExcelService;

class ExcelController extends Controller
{
    public function __construct(
        public ExcelService $excelService
    ) {}

    public function __invoke(ExcelRequest $request) {
      
        $file = $request->file('file');

        $filePath = $this->excelService->getFileAndStartParcing($file);

        return response(['message' => 'File uploaded and processing started', 'filepath' => $filePath]);
    }
}
