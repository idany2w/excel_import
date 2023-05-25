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
    protected $start_row;
    protected $end_row;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $start_row, $end_row)
    {
        $this->path = $path;
        $this->start_row = $start_row;
        $this->end_row = $end_row;
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

        for ($row = $this->start_row; $row <= $this->end_row; $row++) {
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

            // Создайте экземпляр модели Row и сохраните данные
            $rows_upsert_data[] = [
                'id' => $id,
                'name' => $name,
                'date' => $date,
            ];
        }

        Row::upsert($rows_upsert_data, ['name', 'date'], ['name', 'date']);
    }
}
