<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelDataController extends Controller
{
    private $worksheet;

    public function __construct()
    {
        $path = storage_path('app/public/tes_data.xlsx');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($path);
        $this->worksheet = $spreadsheet->getActiveSheet();
    }

    public function index(Request $request)
    {
        // Mendapatkan semua nama kolom dari header
        $headerRow = $this->worksheet->getRowIterator(1)->current();
        $cellIterator = $headerRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        $columns = [];
        $columnDataCounts = [];
        foreach ($cellIterator as $cell) {
            $column = $cell->getColumn();
            $columns[$column] = $cell->getValue();
            $columnDataCounts[$column] = $this->countUniqueValues($column);
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
        $pieData = $this->readColumnData($selectedPieColumn);
        $barData = $this->readColumnData($selectedBarColumn);

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

    public function getChartData(Request $request)
    {
        $pieColumn = $request->input('pie_column');
        $barColumn = $request->input('bar_column');

        $pieData = $this->readColumnData($pieColumn);
        $barData = $this->readColumnData($barColumn);

        return response()->json([
            'pieDataLabels' => array_keys($pieData),
            'pieDataCounts' => array_values($pieData),
            'barDataLabels' => array_keys($barData),
            'barDataCounts' => array_values($barData)
        ]);
    }

    private function readColumnData($column, $maxUniqueValues = 30)
    {
        $data = [];
        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $column . $row->getRowIndex();
            $value = $this->worksheet->getCell($cellCoordinate)->getValue();
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

    private function countUniqueValues($column)
    {
        $uniqueValues = [];
        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $column . $row->getRowIndex();
            $value = $this->worksheet->getCell($cellCoordinate)->getValue();
            if (!in_array($value, $uniqueValues)) {
                $uniqueValues[] = $value;
            }
        }
        return count($uniqueValues);
    }
}