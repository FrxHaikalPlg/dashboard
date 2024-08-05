<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelDataController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ExcelDataController::class, 'index'])->name('excel.data');
