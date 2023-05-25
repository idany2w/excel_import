<?php

namespace App\Jobs\Rows;

use App\Models\Row;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportExcelRowsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $offset;
    protected $limit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $offset = 2, $limit = 1000)
    {
        $this->path = $path;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $spreadsheet = IOFactory::load($this->path);
        $worksheet = $spreadsheet->getActiveSheet();

        $rows_upsert_data = [];

        for ($row = $this->offset; $row < $this->offset + $this->limit; $row++) {
            $id = $worksheet->getCell('A' . $row)->getCalculatedValue();
            $name = $worksheet->getCell('B' . $row)->getCalculatedValue();
            $date = $worksheet->getCell('C' . $row)->getCalculatedValue();

            if(
                empty($id)
                || empty($name)
                || empty($date)
            ){
                break;
            }

            $date = date('Y.m.d', Date::excelToTimestamp($date));

            $rows_upsert_data[] = [
                'id' => $id,
                'name' => $name,
                'date' => $date,
            ];
        }

        Row::upsert($rows_upsert_data, ['name', 'date'], ['name', 'date']);

        self::dispatch($this->path)->onQueue('rows_import_queue');
    }
}
