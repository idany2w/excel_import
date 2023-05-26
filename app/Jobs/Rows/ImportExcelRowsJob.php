<?php

namespace App\Jobs\Rows;

use App\Models\Row;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Rows\Import\ExcelImportProcessEvent;

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
    public function __construct($path, $offset = 1, $limit = 1000)
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

        $cols = [];
        for ($i=1; $i <= 3; $i++) { 
            $cols[$i] = $worksheet->getCell([$i, 1])->getValue();
        }

        $row_index = $this->offset;
        $row_end_index = $this->offset + $this->limit;
        
        if($row_index === 1){
            $row_index = 2;
            $row_end_index++;
        }

        for ($row_index; $row_index < $row_end_index; $row_index++) {
            $cells = [];

            foreach($cols as $col_index => $col_name){
                $cells[$col_name] = $worksheet->getCell([$col_index,  $row_index])->getCalculatedValue();
            }

            if(
                empty($cells['id'])
                || empty($cells['name'])
                || empty($cells['date'])
            ){
                break;
            }

            $cells['date'] = date('Y.m.d', Date::excelToTimestamp($cells['date']));

            $rows_upsert_data[] = [
                'id' => $cells['id'],
                'name' => $cells['name'],
                'date' => $cells['date'],
            ];
        }

        Row::upsert($rows_upsert_data, ['name', 'date'], ['name', 'date']);

        $count_rows_to_import = count($rows_upsert_data);

        $key = md5($this->path);

        Redis::set($key, $this->offset + $count_rows_to_import);

        ExcelImportProcessEvent::dispatch($key);

        if($count_rows_to_import !== 0 && $count_rows_to_import >= $this->limit){
            self::dispatch($this->path, $row_end_index)->onQueue('rows_import_queue');
        }
    }
}
