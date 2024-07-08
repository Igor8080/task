<?php 

namespace App\Imports;

use App\Models\Row;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Support\Facades\Redis;


class RowsImport implements ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, ShouldQueue, WithEvents
{
    private $processedRows = 0;

    public function model(array $row)
    {
        if($row['date'] == null) return ;

        $this->processedRows++;

        return new Row([
            'id' => $row['id'],
            'name' => $row['name'],
            'date' => \Carbon\Carbon::createFromFormat('d.m.Y', $row['date'])->toDateString(),
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                Redis::set("parsing_progress:{$event->reader->getFileName()}", $this->processedRows);
            },
            ImportFailed::class => function(ImportFailed $event) {
                Redis::set("parsing_progress:{$event->reader->getFileName()}", $this->processedRows);
            },
        ];
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }
}