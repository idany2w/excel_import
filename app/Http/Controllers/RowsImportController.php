<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rows\ImportExcelRequest;

class RowsImportController extends Controller
{
    public function uploadExcel(ImportExcelRequest $request)
    {
        $validated = $request->validated();

        $validated = $request->safe(['file']);
    }
}
