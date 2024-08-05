<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelDataController extends Controller
{
    public function index(Request $request)
    {
        $path = storage_path('app/public/tes_data.xlsx');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        // Mendapatkan semua nama kolom dari header
        $headerRow = $worksheet->getRowIterator(1)->current();
        $cellIterator = $headerRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        $columns = [];
        $columnDataCounts = [];
        foreach ($cellIterator as $cell) {
            $column = $cell->getColumn();
            $columns[$column] = $cell->getValue();
            $columnDataCounts[$column] = $this->countUniqueValues($worksheet, $column);
        }

        // Filter kolom yang memiliki terlalu banyak nilai unik
        $maxUniqueValues = 30; // Batas maksimum nilai unik
        $filteredColumns = array_filter($columns, function ($key) use ($columnDataCounts, $maxUniqueValues) {
            return $columnDataCounts[$key] <= $maxUniqueValues;
        }, ARRAY_FILTER_USE_KEY);

        // Menangani pemilihan kolom untuk pie chart dan bar chart
        $selectedPieColumn = $request->input('pie_column', array_keys($filteredColumns)[0] ?? null);
        $selectedBarColumn = $request->input('bar_column', array_keys($filteredColumns)[0] ?? null);

        // Membaca data dari kolom yang dipilih untuk pie chart dan bar chart
        $pieData = $this->readColumnData($worksheet, $selectedPieColumn);
        $barData = $this->readColumnData($worksheet, $selectedBarColumn);

        return view('welcome', [
            'columns' => $filteredColumns,
            'selectedPieColumn' => $selectedPieColumn,
            'selectedBarColumn' => $selectedBarColumn,
            'pieDataLabels' => $pieData ? array_keys($pieData) : [],
            'pieDataCounts' => $pieData ? array_values($pieData) : [],
            'barDataLabels' => $barData ? array_keys($barData) : [],
            'barDataCounts' => $barData ? array_values($barData) : [],
            'error' => $error ?? null
        ]);
    }

    private function readColumnData($worksheet, $column, $maxUniqueValues = 30)
    {
        $data = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $column . $row->getRowIndex();
            $value = $worksheet->getCell($cellCoordinate)->getValue();
            if ($value !== null && $value !== '') {
                if (!isset($data[$value])) {
                    $data[$value] = 0;
                }
                $data[$value]++;
            }
        }

        if (count($data) > $maxUniqueValues) {
            return null; // atau bisa juga throw new \Exception("Data exceeds maximum unique values.");
        }

        return $data;
    }

    private function countUniqueValues($worksheet, $column)
    {
        $uniqueValues = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $column . $row->getRowIndex();
            $value = $worksheet->getCell($cellCoordinate)->getValue();
            if (!in_array($value, $uniqueValues)) {
                $uniqueValues[] = $value;
            }
        }
        return count($uniqueValues);
    }
}