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
        $cities = $this->fetchCities();
        $generations = $this->calculateGenerations();
        $barData = $this->fetchBarData();
        $jenisKelaminData = $this->fetchJenisKelaminData();

        return view('welcome', [
            'barDataLabels' => $barData ? array_keys($barData) : [],
            'barDataCounts' => $barData ? array_values($barData) : [],
            'generations' => $generations,
            'cities' => $cities,
            'jenisKelaminLabels' => $jenisKelaminData ? array_keys($jenisKelaminData) : [],
            'jenisKelaminCounts' => $jenisKelaminData ? array_values($jenisKelaminData) : [],
            'error' => $error ?? null
        ]);
    }

    private function fetchCities()
    {
        $unitKerjaToCityMap = $this->getUnitKerjaToCityMap();
        $unitKerjaColumn = $this->findColumn('unit kerja');
        $cities = [];

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
        return $cities;
    }

    private function getUnitKerjaToCityMap()
    {
        return [
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
    }

    private function calculateGenerations()
    {
        $tanggalLahirColumn = $this->findColumn('tanggal lahir');
        $generations = [
            'Baby Boomer' => 0,
            'Gen X' => 0,
            'Gen Y / Milenial' => 0,
            'Gen Z' => 0,
        ];

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $tanggalLahirColumn . $row->getRowIndex();
            $cellValue = $this->worksheet->getCell($cellCoordinate)->getValue();
            if ($cellValue && ExcelDate::isDateTime($this->worksheet->getCell($cellCoordinate))) {
                $tanggalLahir = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                $year = date('Y', strtotime($tanggalLahir));
                if ($year <= 1964) $generations['Baby Boomer']++;
                elseif ($year >= 1965 && $year <= 1980) $generations['Gen X']++;
                elseif ($year >= 1981 && $year <= 1996) $generations['Gen Y / Milenial']++;
                elseif ($year >= 1997) $generations['Gen Z']++;
            }
        }
        return $generations;
    }

    private function fetchBarData()
    {
        $selectedBarColumn = $this->findColumn('role');
        return $this->readColumnData($selectedBarColumn);
    }

    private function fetchJenisKelaminData()
    {
        $jenisKelaminColumn = $this->findColumn('jenis kelamin');
        return $this->readColumnData($jenisKelaminColumn);
    }

    private function findColumn($columnName)
    {
        $firstRow = $this->worksheet->getRowIterator(1)->current();
        $cellIterator = $firstRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            if (strtolower(trim($cell->getValue())) === strtolower($columnName)) {
                return $cell->getColumn();
            }
        }
        throw new \Exception("Column '$columnName' not found in the Excel file.");
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