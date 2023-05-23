<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rows\ImportExcelRequest;

class ImportController extends Controller
{
    public function uploadExcel(ImportExcelRequest $request)
    {
        $validated = $request->validated();

        $validated = $request->safe(['file']);
    }
}
