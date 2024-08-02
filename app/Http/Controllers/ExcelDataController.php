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
        foreach ($cellIterator as $cell) {
            $columns[$cell->getColumn()] = $cell->getValue();
        }

        $selectedColumn = $request->input('column', array_keys($columns)[0] ?? null);

        if ($selectedColumn === null) {
            throw new \Exception("Tidak ada kolom yang dipilih atau file tidak memiliki kolom.");
        }

        // Membaca data dari kolom yang dipilih
        $data = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $selectedColumn . $row->getRowIndex();
            $value = $worksheet->getCell($cellCoordinate)->getValue();
            if ($value !== null && $value !== '') {
                if (!isset($data[$value])) {
                    $data[$value] = 0;
                }
                $data[$value]++;
            }
        }

        $dataLabels = array_keys($data);
        $dataCounts = array_values($data);

        return view('tes', [
            'columns' => $columns,
            'selectedColumn' => $selectedColumn,
            'dataLabels' => $dataLabels,
            'dataCounts' => $dataCounts
        ]);
    }
}