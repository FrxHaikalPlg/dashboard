<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Storage;

class ExcelDataController extends Controller
{
    protected $worksheet;

    public function __construct()
    {
        $this->loadSpreadsheet(storage_path('app/public/tes_data.xlsx'));
    }

    public function loadSpreadsheet($path)
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($path);
        $this->worksheet = $spreadsheet->getActiveSheet();
    }

    public function index(Request $request)
    {
        // Load the spreadsheet
        $path = storage_path('app/public/tes_data.xlsx');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($path);
        $this->worksheet = $spreadsheet->getActiveSheet();

        // Define the mapping from 'unit kerja' to cities
        $unitKerjaToCityMap = [
            "KACAB BANDAR LAMPUNG" => "BANDAR LAMPUNG",
            "KACAB BENGKULU" => "BENGKULU",
            "KACAB JAMBI" => "JAMBI",
            "KACAB LAMPUNG TENGAH" => "LAMPUNG TENGAH",
            "KACAB MUARA ENIM" => "MUARA ENIM",
            "KACAB MUARO BUNGO" => "MUARO BUNGO",
            "KACAB PALEMBANG" => "PALEMBANG",
            "KACAB PANGKAL PINANG" => "PANGKAL PINANG",
            "KANWIL SUMBAGSEL" => "KANWIL",
            "KCP BANYUASIN PANGKALAN BALAI" => "PALEMBANG",
            "KCP BATANGHARI MUARA BULIAN" => "JAMBI",
            "KCP BELITUNG TANJUNG PANDAN" => "PANGKAL PINANG",
            "KCP CABANG ARGA MAKMUR" => "BENGKULU",
            "KCP KOTA METRO NASUTION" => "BANDAR LAMPUNG",
            "KCP LAHAT PASAR LAMA" => "MUARA ENIM",
            "KCP LAMPUNG SELATAN KALIANDA" => "BANDAR LAMPUNG",
            "KCP LAMPUNG UTARA KOTABUMI" => "LAMPUNG TENGAH",
            "KCP LUBUK LINGGAU YOS SUDARSO" => "MUARA ENIM",
            "KCP MERANGIN BANGKO" => "MUARO BUNGO",
            "KCP MUARO JAMBI SENGETI" => "JAMBI",
            "KCP OGAN KOMERING ULU BATURAJA TIMUR" => "MUARA ENIM",
            "KCP PRABUMULIH SUDIRMAN" => "MUARA ENIM",
            "KCP PRINGSEWU SUDIRMAN" => "BANDAR LAMPUNG",
            "KCP REJANG LEBONG CURUP" => "BENGKULU",
            "KCP SAROLANGUN LINTAS SUMATERA" => "MUARO BUNGO",
            "KCP SUNGAI PENUH YOS SUDARSO" => "MUARO BUNGO",
            "KCP TANJUNG JABUNG BARAT KUALA TUNGKAL" => "JAMBI",
            "KCP TEBO LINTAS" => "MUARO BUNGO",
            "KCP TULANG BAWANG BANJAR AGUNG" => "LAMPUNG TENGAH",
        ];

        // Find the 'unit kerja' column and fetch cities
        $unitKerjaColumn = null;
        $cities = [];
        $firstRow = $this->worksheet->getRowIterator(1)->current();
        $cellIterator = $firstRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            if (strtolower(trim($cell->getValue())) === 'unit kerja') {
                $unitKerjaColumn = $cell->getColumn();
                break;
            }
        }

        if ($unitKerjaColumn === null) {
            throw new \Exception("Column 'Unit Kerja' not found in the Excel file.");
        }

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $unitKerjaColumn . $row->getRowIndex();
            $unitKerja = $this->worksheet->getCell($cellCoordinate)->getValue();
            if (isset($unitKerjaToCityMap[$unitKerja])) {
                $city = $unitKerjaToCityMap[$unitKerja];
                if (!in_array($city, $cities)) {
                    $cities[] = $city;
                }
            }
        }

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

        $maxUniqueValues = 30; // Batas maksimum nilai unik
        $filteredColumns = array_filter($columns, function ($key) use ($columnDataCounts, $maxUniqueValues) {
            return $columnDataCounts[$key] <= $maxUniqueValues;
        }, ARRAY_FILTER_USE_KEY);

        $generations = [
            'Baby Boomer' => 0,
            'Gen X' => 0,
            'Gen Y / Milenial' => 0,
            'Gen Z' => 0,
        ];

        $tanggalLahirList = []; // Array to hold all dates
        $tanggalLahirColumn = null; // Initialize column for 'tanggal_lahir'

        // Scan the first row to find the 'tanggal_lahir' column
        $firstRow = $this->worksheet->getRowIterator(1)->current();
        $cellIterator = $firstRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            if (strtolower(trim($cell->getValue())) === 'tanggal lahir') {
                $tanggalLahirColumn = $cell->getColumn();
            }
            elseif(strtolower(trim($cell->getValue())) === 'role'){
                $selectedBarColumn = $cell->getColumn();
                $barData = $this->readColumnData($selectedBarColumn);
            }
            elseif(strtolower(trim($cell->getValue())) === 'jenis kelamin'){
                $jenisKelaminColumn = $cell->getColumn();
                $jenisKelaminData = $this->readColumnData($jenisKelaminColumn);

            }
        }

        if ($tanggalLahirColumn === null) {
            throw new \Exception("Column 'Tanggal Lahir' not found in the Excel file.");
        }

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $tanggalLahirColumn . $row->getRowIndex();
            $cellValue = $this->worksheet->getCell($cellCoordinate)->getValue();
            if ($cellValue && ExcelDate::isDateTime($this->worksheet->getCell($cellCoordinate))) {
                $tanggalLahir = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                $tanggalLahirList[] = $tanggalLahir;
                $year = date('Y', strtotime($tanggalLahir));
                if ($year <= 1964) $generations['Baby Boomer']++;
                elseif ($year >= 1965 && $year <= 1980) $generations['Gen X']++;
                elseif ($year >= 1981 && $year <= 1996) $generations['Gen Y / Milenial']++;
                elseif ($year >= 1997) $generations['Gen Z']++;
            }
        }

        // Read "Jenis Kelamin" data

        // Combine and return view
        return view('welcome', [
            'columns' => $filteredColumns,
            'selectedBarColumn' => $selectedBarColumn,
            'barDataLabels' => $barData ? array_keys($barData) : [],
            'barDataCounts' => $barData ? array_values($barData) : [],
            'generations' => $generations,
            'tanggalLahirList' => $tanggalLahirList,
            'cities' => $cities,
            'jenisKelaminLabels' => $jenisKelaminData ? array_keys($jenisKelaminData) : [],
            'jenisKelaminCounts' => $jenisKelaminData ? array_values($jenisKelaminData) : [],
            'error' => $error ?? null
        ]);
    }

    private function readColumnData($column)
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

    

   

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->storeAs('public', 'tes_data.xlsx');

        $this->loadSpreadsheet(storage_path('app/' . $path));

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }
}