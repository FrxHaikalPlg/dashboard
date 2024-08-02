<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('tes');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $path = $request->file('file')->getRealPath();
        $data = Excel::toArray(new EmployeesImport, $path);

        $generations = [
            'Baby Boomer' => 0,
            'Gen X' => 0,
            'Gen Y / Milenial' => 0,
            'Gen Z' => 0,
        ];

        foreach ($data[0] as $row) {
            $year = date('Y', strtotime($row['tanggal_lahir']));
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

        return view('tes', compact('generations'));
    }
}