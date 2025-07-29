<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Agenda::whereDate('start', '>=', $request->start)
                          ->whereDate('end',   '<=', $request->end)
                          ->get(['kd_agenda as id', 'title', 'start', 'end']);
            
            return response()->json($data);
        }
        return view('karyawan.agenda.agenda');
    }

    public function ajax(Request $request)
    {
        switch ($request->type) {
           case 'add':
              $event = Agenda::create([
                  'title' => $request->title,
                  'start' => $request->start,
                  'end' => $request->end,
                  'kd_karyawan' => Auth::guard('karyawan')->id(),
                  'dibuat_oleh' => Auth::guard('karyawan')->user()->nama,
              ]);
 
              return response()->json($event);
             break;
  
           case 'update':
              $event = Agenda::find($request->id)->update([
                  'title' => $request->title,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
 
              return response()->json($event);
             break;
  
           case 'delete':
              $event = Agenda::find($request->id)->delete();
  
              return response()->json($event);
             break;
             
           default:
             break;
        }
    }
}