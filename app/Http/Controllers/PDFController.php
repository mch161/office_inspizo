<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function card(Request $request)
    {
        $data = Karyawan::find($request->id ?? Auth::guard('karyawan')->id())->toArray();


        $pdf = Pdf::loadView('pdf.card', $data)
                    ->setPaper('a4', 'landscape');

        return $pdf->download('card '.$data['kd_karyawan'].'.pdf');
    }
}
