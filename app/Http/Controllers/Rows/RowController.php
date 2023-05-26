<?php

namespace App\Http\Controllers\Rows;

use App\Models\Row;
use App\Http\Controllers\Controller;

class RowController extends Controller
{
    public function index()
    {
        $rows = Row::orderByDesc('date')->get();

        $result = $rows->groupBy('date');

        return response($result);
    }
}
