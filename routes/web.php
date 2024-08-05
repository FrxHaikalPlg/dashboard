<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelDataController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ExcelDataController::class, 'index'])->name('excel.data');

Route::get('/tes', function () {
    return view('tes');
});

Route::get('/tes', [ExcelDataController::class, 'index'])->name('excel.data');