<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EmployeeController extends Controller
{
    public function index()
    {
        // Path to the existing Excel file
        $path = storage_path('app/public/tes_data.xlsx');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $generations = [
            'Baby Boomer' => 0,
            'Gen X' => 0,
            'Gen Y / Milenial' => 0,
            'Gen Z' => 0,
        ];

        $tanggalLahirList = []; // Array to hold all dates
        $tanggalLahirColumn = null; // Initialize column for 'tanggal_lahir'

        // Scan the first row to find the 'tanggal_lahir' column
        $firstRow = $worksheet->getRowIterator(1)->current();
        $cellIterator = $firstRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            // Use strtolower and trim to handle case insensitivity and extra spaces
            if (strtolower(trim($cell->getValue())) === 'tanggal lahir') {
                $tanggalLahirColumn = $cell->getColumn();
                break;
            }
        }

        if ($tanggalLahirColumn === null) {
            throw new \Exception("Column 'Tanggal Lahir' not found in the Excel file.");
        }

        // Process rows starting from row 2 (assuming row 1 is the header)
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $tanggalLahirColumn . $row->getRowIndex();
            $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
            if ($cellValue) {
                // Check if the cell value is a date
                if (ExcelDate::isDateTime($worksheet->getCell($cellCoordinate))) {
                    $tanggalLahir = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                } else {
                    $tanggalLahir = $cellValue; // Use the value as is if it's not a date
                }
                $tanggalLahirList[] = $tanggalLahir; // Add date to the list
                $year = date('Y', strtotime($tanggalLahir));
                if ($year <= 1964) {
                    $generations['Baby Boomer']++;
                } elseif ($year >= 1965 && $year <= 1980) {
                    $generations['Gen X']++;
                } elseif ($year >= 1981 && $year <= 1996) {
                    $generations['Gen Y / Milenial']++;
                } elseif ($year >= 1997 && $year <= 2012) {
                    $generations['Gen Z']++;
                }
            }
        }

        return view('tes', [
            'generations' => $generations,
            'pieDataLabels' => array_keys($generations),
            'pieDataCounts' => array_values($generations),
            'tanggalLahirList' => $tanggalLahirList
        ]);
    }
}