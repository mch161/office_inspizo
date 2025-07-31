<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        return view('karyawan.agenda.agenda');
    }

    public function fetch(Request $request)
    {
        $data = Agenda::whereDate('start', '<=', $request->end)
            ->whereDate('end', '>=', $request->start)
            ->get(['kd_agenda as id', 'title', 'start', 'end']);

        return response()->json($data);
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