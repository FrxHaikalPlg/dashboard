<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelDataController;
use App\Http\Controllers\EmployeeController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ExcelDataController::class, 'index'])->name('excel.data');

Route::get('/api/chart-data', [ExcelDataController::class, 'getChartData'])->name('api.chart-data');
Route::get('/tes', [EmployeeController::class, 'index'])->name('tes');