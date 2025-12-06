<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pekerjaan;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class SignatureController extends Controller
{
    /**
     * Mapping model, kolom, dan rute untuk fungsionalitas universal.
     * Format: [Model Class, 'Model DB Key', 'Route Param Name', 'Redirect Route Name', 'TTD Column/Relation']
     */
    private $modelMap = [
        'pesanan' => [Pesanan::class, 'kd_pesanan', 'tiket', 'tiket.show', 'relation:signature'],

        'pekerjaan' => [Pekerjaan::class, 'kd_pekerjaan', 'pekerjaan', 'pekerjaan.show', 'column:ttd_pelanggan'],
    ];

    /**
     * Menampilkan formulir tanda tangan.
     *
     * @param string $type Tipe model ('pesanan' atau 'pekerjaan').
     * @param int $id ID item.
     * @return \Illuminate\View\View
     */
    public function index($type, $id, $id2 = null)
    {
        if ($id2) {
            $type = $id;
            $id = $id2;
        }
        if (!isset($this->modelMap[$type])) {
            abort(404, 'Tipe model tidak valid.');
        }

        $map = $this->modelMap[$type];
        $modelClass = $map[0];
        $dbKeyName = $map[1];
        $ttdSource = $map[4];

        $item = $modelClass::find($id);

        $currentUrl = url()->current();
        $normalizedUrl = rtrim($currentUrl, '/');

        $backUrl = dirname($normalizedUrl);

        if (!$item) {
            return view('error', ['message' => ucfirst($type) . ' tidak ditemukan.']);
        }

        $signature = null;

        if ($ttdSource === 'relation:signature') {
            $signature = Signature::where($dbKeyName, $id)->first();
        } else if ($ttdSource === 'column:ttd_pelanggan') {
            if ($item->ttd_pelanggan) {
                $signature = (object) ['signature' => $item->ttd_pelanggan];
            }
        }

        return view('karyawan.tiket.signature', compact('item', 'signature', 'type', 'map', 'backUrl'));
    }

    /**
     * Menyimpan data tanda tangan.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type Tipe model ('pesanan' atau 'pekerjaan').
     * @param int $id ID item.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'signed' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        if (!isset($this->modelMap[$type])) {
            return redirect()->back()->with('error', 'Tipe model tidak valid.');
        }

        $map = $this->modelMap[$type];
        $modelClass = $map[0];
        $dbKeyName = $map[1];
        $routeParamName = $map[2];
        $redirectRoute = $map[3];
        $ttdSource = $map[4];

        $item = $modelClass::find($id);

        if (!$item) {
            return redirect()->back()->with('error', ucfirst($type) . ' tidak ditemukan.');
        }

        $signedData = $request->signed;

        if ($ttdSource === 'relation:signature') {
            Signature::where($dbKeyName, $id)->delete();
            Signature::create([
                $dbKeyName => $id,
                'signature' => $signedData,
            ]);

        } else if ($ttdSource === 'column:ttd_pelanggan') {
            $item->update([
                'ttd_pelanggan' => $signedData,
            ]);
        }

        return redirect()->route($redirectRoute, [$routeParamName => $id])->with('success', 'Signature berhasil disimpan.');
    }
}