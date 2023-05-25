<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use App\Jobs\Rows\ImportExcelRowsJob;
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

        ImportExcelRowsJob::dispatch($path)->onQueue('rows_import_queue');

        return response(
            [
                'nessage' => 'file uploaded'
            ]
        );
    }
}
