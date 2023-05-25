<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use App\Jobs\Rows\ImportExcelRowsJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Requests\Rows\ImportExcelRequest;

class ImportController extends Controller
{
    public function importExcel(ImportExcelRequest $request)
    {
        $file = $request->file('file');

        $file_name = $file->getClientOriginalName();

        $user_id = auth()->user()->id;

        $path = $file->storeAs(
            "import_files/{$user_id}",
            $file_name,
        );

        $path = storage_path("app/{$path}");

        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows_count = $worksheet->getHighestRow();

        $chunk_rows = 1000;
        $start_row = 2;

        while ($start_row <= $rows_count) {
            $endRow = $start_row + $chunk_rows - 1;

            ImportExcelRowsJob::dispatch($path, $start_row, $endRow)->onQueue('rows_import_queue');

            $start_row += $chunk_rows;
        }
    }
}
