<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ChartController extends Controller
{
    public function renderChart()
    {
        $path = storage_path('app/public/DATA_JULI_2024.xlsx');
        $data = $this->readExcel($path);

        // Logika untuk membuat grafik dengan data
        return view('chart', ['data' => $data]);
    }

    private function readExcel($path)
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach
        