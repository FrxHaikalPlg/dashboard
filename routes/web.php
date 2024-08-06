<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelDataController;
use App\Http\Controllers\EmployeeController;

// Ensure this is the only route for '/'
Route::get('/', [ExcelDataController::class, 'index'])->name('excel.data');

Route::get('/api/chart-data', [ExcelDataController::class, 'getChartData'])->name('api.chart-data');
Route::get('/tes', [EmployeeController::class, 'index'])->name('tes');
Route::post('/upload-file', [ExcelDataController::class, 'uploadFile'])->name('upload.file');
Route::get('/city', [ExcelDataController::class, 'index'])->name('filter.by.city');