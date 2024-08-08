<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

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
        $city = $request->query('city'); // Mengambil kota dari query parameter
        $cities = $this->fetchCities();
        $generations = $this->calculateGenerations($city);
        $barData = $this->fetchBarData($city);
        $jenisKelaminData = $this->fetchJenisKelaminData($city);
        $excelData = [];
        $columnNames = [];
        $error = null;

        try {
            $excelData = $this->fetchExcelData($city);
            $columnNames = $this->getColumnNames();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('welcome', [
            'barDataLabels' => $barData ? array_keys($barData) : [],
            'barDataCounts' => $barData ? array_values($barData) : [],
            'generations' => $generations,
            'cities' => $cities,
            'jenisKelaminLabels' => $jenisKelaminData ? array_keys($jenisKelaminData) : [],
            'jenisKelaminCounts' => $jenisKelaminData ? array_values($jenisKelaminData) : [],
            'selectedCity' => $city,
            'excelData' => $excelData,
            'columnNames' => $columnNames,
            'error' => $error
        ]);
    }

    private function fetchCities()
    {
        $unitKerjaToCityMap = $this->getUnitKerjaToCityMap();
        return array_keys($unitKerjaToCityMap);
    }

    private function getUnitKerjaToCityMap()
    {
        return [
            "BANDAR LAMPUNG" => [
                "KACAB BANDAR LAMPUNG",
                "KCP KOTA METRO NASUTION",
                "KCP LAMPUNG SELATAN KALIANDA",
                "KCP PRINGSEWU SUDIRMAN"
            ],
            "BENGKULU" => [
                "KACAB BENGKULU",
                "KCP CABANG ARGA MAKMUR",
                "KCP REJANG LEBONG CURUP"
            ],
            "JAMBI" => [
                "KACAB JAMBI",
                "KCP BATANGHARI MUARA BULIAN",
                "KCP MUARO JAMBI SENGETI",
                "KCP TANJUNG JABUNG BARAT KUALA TUNGKAL"
            ],
            "LAMPUNG TENGAH" => [
                "KACAB LAMPUNG TENGAH",
                "KCP LAMPUNG UTARA KOTABUMI",
                "KCP TULANG BAWANG BANJAR AGUNG"
            ],
            "MUARA ENIM" => [
                "KACAB MUARA ENIM",
                "KCP LAHAT PASAR LAMA",
                "KCP LUBUK LINGGAU YOS SUDARSO",
                "KCP OGAN KOMERING ULU BATURAJA TIMUR",
                "KCP PRABUMULIH SUDIRMAN"
            ],
            "MUARO BUNGO" => [
                "KACAB MUARO BUNGO",
                "KCP MERANGIN BANGKO",
                "KCP SAROLANGUN LINTAS SUMATERA",
                "KCP SUNGAI PENUH YOS SUDARSO",
                "KCP TEBO LINTAS"
            ],
            "PALEMBANG" => [
                "KACAB PALEMBANG",
                "KCP BANYUASIN PANGKALAN BALAI"
            ],
            "PANGKAL PINANG" => [
                "KACAB PANGKAL PINANG",
                "KCP BELITUNG TANJUNG PANDAN"
            ],
            "KANWIL" => [
                "KANWIL SUMBAGSEL"
            ]
        ];
    }

    private function calculateGenerations($city = null)
    {
        $unitKerjaToCityMap = $this->getUnitKerjaToCityMap();
        $unitKerjas = $city ? $unitKerjaToCityMap[$city] : Arr::flatten($unitKerjaToCityMap);
        $tanggalLahirColumn = $this->findColumn('tanggal lahir');
        $unitKerjaColumn = $this->findColumn('unit kerja'); // Menemukan kolom unit kerja di luar loop
        $generations = [
            'Baby Boomer' => 0,
            'Gen X' => 0,
            'Gen Y / Milenial' => 0,
            'Gen Z' => 0,
        ];

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $tanggalLahirColumn . $row->getRowIndex();
            $cellValue = $this->worksheet->getCell($cellCoordinate)->getValue();
            $unitKerjaCoordinate = $unitKerjaColumn . $row->getRowIndex(); // Menggunakan kolom unit kerja yang ditemukan
            $unitKerja = $this->worksheet->getCell($unitKerjaCoordinate)->getValue();
            if (in_array($unitKerja, $unitKerjas) && $cellValue && ExcelDate::isDateTime($this->worksheet->getCell($cellCoordinate))) {
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
    protected function getRoleFromJabatan($jabatan) {
        if (empty(trim($jabatan))) {
            return null; 
        }
    
        
    $roleMappings = [
        '/Account Representative|Petugas Administrasi Peserta|Relationship Manager/' => 'Kepesertaan',
        '/Customer Service Officer|Manajer Kasus|Penata Pelayanan|Layanan/' => 'Pelayanan',
        '/Kepala Bidang/' => 'Kabid',
        '/Kepala Kantor Cabang/' => 'Kepala Cabang',
        '/Penata Keuangan|Penata Operasional Cabang|Penata Pengendalian dan Risiko|Penata Pengendalian Operasional /' => 'Pengendalian Operasional',
        '/Petugas Pemeriksa/' => 'Wasrik',
        '/Wakil Kepala Wilayah/' => 'Wakil Kepala Wilayah',
        '/Human Capital|IT Solution|Penata Sarana dan Prasarana|Penata Kesekretariatan/' => 'DHCA',
        '/Penata Tata Kelola, Risiko, dan Kepatuhan|Penata Pengendalian Operasional Wilayah/' => 'Keuangan dan MR'
    ];

    foreach ($roleMappings as $pattern => $role) {
        if (preg_match($pattern, $jabatan)) {
            return $role;
        }
    }

    return $jabatan; 

}
    private function fetchBarData($city = null)
{
    $unitKerjaToCityMap = $this->getUnitKerjaToCityMap();
    $unitKerjas = $city ? $unitKerjaToCityMap[$city] : Arr::flatten($unitKerjaToCityMap);
    $jabatanColumn = $this->findColumn('jabatan');
    $barData = [];

    foreach ($this->worksheet->getRowIterator(2) as $row) {
        $cellCoordinate = $jabatanColumn . $row->getRowIndex();
        $jabatan = $this->worksheet->getCell($cellCoordinate)->getValue();
        if ($jabatan !== null && $jabatan !== '') { 
            $role = $this->getRoleFromJabatan($jabatan); 

            if (!isset($barData[$role])) {
                $barData[$role] = 0;
            }
            $barData[$role]++;
        }
    }
    return $barData;
}

    private function fetchJenisKelaminData($city = null)
    {
        $unitKerjaToCityMap = $this->getUnitKerjaToCityMap();
        $unitKerjas = $city ? $unitKerjaToCityMap[$city] : Arr::flatten($unitKerjaToCityMap);
        $jenisKelaminColumn = $this->findColumn('jenis kelamin');
        $unitKerjaColumn = $this->findColumn('unit kerja'); // Menemukan kolom unit kerja
        $jenisKelaminData = [];

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $jenisKelaminColumn . $row->getRowIndex();
            $value = $this->worksheet->getCell($cellCoordinate)->getValue();
            $unitKerjaCoordinate = $unitKerjaColumn . $row->getRowIndex(); // Menggunakan kolom unit kerja yang ditemukan
            $unitKerja = $this->worksheet->getCell($unitKerjaCoordinate)->getValue();
            if (in_array($unitKerja, $unitKerjas) && $value !== null && $value !== '') {
                if (!isset($jenisKelaminData[$value])) {
                    $jenisKelaminData[$value] = 0;
                }
                $jenisKelaminData[$value]++;
            }
        }
        return $jenisKelaminData;
    }

    private function fetchExcelData($city = null)
    {
        $unitKerjaToCityMap = $this->getUnitKerjaToCityMap();
        $unitKerjas = $city ? $unitKerjaToCityMap[$city] : Arr::flatten($unitKerjaToCityMap);
        $data = [];

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $rowIndex = $row->getRowIndex();
            $unitKerja = $this->worksheet->getCell($this->findColumn('unit kerja') . $rowIndex)->getValue();
            if (in_array($unitKerja, $unitKerjas)) {
                $rowData = [];
                foreach ($this->getColumnNames() as $columnName) {
                    $cellValue = $this->worksheet->getCell($this->findColumn($columnName) . $rowIndex)->getValue();
                    if (strtolower(trim($columnName)) === 'tanggal lahir' && ExcelDate::isDateTime($this->worksheet->getCell($this->findColumn($columnName) . $rowIndex))) {
                        $cellValue = ExcelDate::excelToDateTimeObject($cellValue)->format('d/m/Y');
                    }
                    $rowData[$columnName] = $cellValue;
                }
                $data[] = $rowData;
            }
        }
        return $data;
    }

    private function getColumnNames()
    {
        $firstRow = $this->worksheet->getRowIterator(1)->current();
        $cellIterator = $firstRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        $columnNames = [];
        foreach ($cellIterator as $cell) {
            $columnName = $cell->getValue();
            if (strtolower(trim($columnName)) !== 'no.') {
                $columnNames[] = $columnName;
            }
        }
        return $columnNames;
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