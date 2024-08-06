<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class EmployeeController extends Controller
{
    public function index()
    {
        $path = storage_path('app/public/tes_data.xlsx');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $unitKerjaColumn = null;
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

        $cities = [];

        // Find the 'unit kerja' column
        $firstRow = $worksheet->getRowIterator(1)->current();
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

        // Process rows to map 'unit kerja' to cities
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellCoordinate = $unitKerjaColumn . $row->getRowIndex();
            $unitKerja = $worksheet->getCell($cellCoordinate)->getValue();
            if (isset($unitKerjaToCityMap[$unitKerja])) {
                $city = $unitKerjaToCityMap[$unitKerja];
                if (!in_array($city, $cities)) {
                    $cities[] = $city;
                }
            }
        }

        return view('tes', ['cities' => $cities]);
    }
}