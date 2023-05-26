<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rows\RowController;
use App\Http\Controllers\Rows\ImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth.basic')
    ->post('/rows/import/excel', [ImportController::class, 'importExcel'])
    ->name('rows.import.excel');

Route::get('/rows', [RowController::class, 'index'])->name('rows.index');