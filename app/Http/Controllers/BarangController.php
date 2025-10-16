<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;


class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        $query->when($request->filled('s'), function ($q) use ($request) {
            return $q->where('nama_barang', 'LIKE', "%" . $request->input('s') . "%");
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('dijual', $request->input('status'));
        });

        $query->when($request->filled('kondisi'), function ($q) use ($request) {
            return $q->where('kondisi', $request->input('kondisi'));
        });

        $query->when($request->filled('kategori'), function ($q) use ($request) {
            return $q->where('kategori', $request->input('kategori'));
        });

        $query->when($request->filled('kode'), function ($q) use ($request) {
            return $q->where('kode', $request->input('kode'));
        });

        $query->when($request->filled('klasifikasi'), function ($q) use ($request) {
            return $q->where('klasifikasi', $request->input('klasifikasi'));
        });

        $sort = $request->input('sort', 'asc');
        switch ($sort) {
            case 'desc':
                $query->orderBy('nama_barang', 'desc');
                break;
            case 'price-asc':
                $query->orderByRaw("CAST(harga_jual AS UNSIGNED) ASC");
                break;
            case 'price-desc':
                $query->orderByRaw("CAST(harga_jual AS UNSIGNED) DESC");
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('nama_barang', 'asc');
                break;
        }

        $barang = $query->distinct()->get();

        return view('karyawan.barang.barang', compact('barang'));
    }

    public function search(Request $request)
    {
        $search = $request->input('q');
        $barangs = Barang::where('nama_barang', 'LIKE', "%{$search}%")->get();
        $response = [];
        foreach ($barangs as $barang) {
            $response[] = [
                "id" => $barang->kd_barang,
                "text" => $barang->nama_barang
            ];
        }

        return response()->json($response);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), ([
            'nama_barang' => 'required|string',
            'hpp' => 'nullable|numeric',
            'harga' => 'nullable|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'stok' => 'nullable|numeric|min:0',
        ]));

        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first())->withInput();
        }

        if ($request->foto) {
            $image = $request->foto;
            $imageName = time() . '.' . $request->foto->extension();
            $path = public_path('storage/images/barang');
            $img = Image::read($image->path());
            $img->scale(width: 480)->save($path . '/' . $imageName);
        } else {
            $imageName = null;
        }

        $barang = new Barang();
        $barang->kd_karyawan = Auth::id();
        $barang->nama_barang = $request->nama_barang;
        $barang->barcode = $request->barcode;
        $barang->klasifikasi = $request->klasifikasi;
        $barang->dijual = $request->dijual;
        $barang->kategori = $request->kategori;
        $barang->kondisi = $request->kondisi;
        $barang->keterangan = $request->keterangan;
        $barang->kode = $request->kode;
        $barang->hpp = $request->hpp;
        $barang->harga_jual = $request->harga;
        $barang->stok = $request->stok ?? 1;
        $barang->foto = $imageName;
        $barang->dibuat_oleh = Auth::user()->nama;
        $barang->save();

        $queryString = $request->_query_string ? '?' . $request->_query_string : '';
        $redirectUrl = route('barang.index') . $queryString;

        if ($barang) {
            return redirect()->to($redirectUrl)->with('success', 'Barang berhasil dibuat.');
        } else {
            return redirect()->to($redirectUrl)->with('error', 'Barang gagal dibuat.');
        }
    }

    public function show(string $id)
    {
        $barang = Barang::find($id);
        $stok = Stok::where('kd_barang', $barang->kd_barang)->orderBy('created_at', 'desc')->get();
        $barcodeIMG = null;
        if ($barang->barcode) {
            $barcode = (new \Picqer\Barcode\Types\TypeEan13())->getBarcode($barang->barcode);
            $renderer = new \Picqer\Barcode\Renderers\HtmlRenderer();
            $barcodeIMG = $renderer->render($barcode, 450.20, 75);
        }
        return view('karyawan.barang.detail', compact('barang', 'stok', 'barcodeIMG'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Barang $barang)
    {
        $validate = Validator::make($request->all(), ([
            'nama_barang' => 'required|string',
            'hpp' => 'nullable|numeric',
            'harga' => 'nullable|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]));

        if ($validate->fails()) {
            return redirect()->route('barang.index')->with('error', $validate->errors()->first());
        }

        if ($request->foto) {
            $image = $request->foto;
            $imageName = time() . '.' . $request->foto->extension();
            if (Storage::disk('public')->exists('images/barang/' . $barang->foto)) {
                Storage::disk('public')->delete('images/barang/' . $barang->foto);
            }
            $path = public_path('storage/images/barang');
            $img = Image::read($image->path());
            $img->scale(width: 480)->save($path . '/' . $imageName);
        } else {
            $imageName = $barang->foto;
        }

        $barang->kd_karyawan = Auth::id();
        $barang->nama_barang = $request->nama_barang;
        $barang->barcode = $request->barcode;
        $barang->klasifikasi = $request->klasifikasi;
        $barang->dijual = $request->dijual;
        $barang->kategori = $request->kategori;
        $barang->kondisi = $request->edit_kondisi;
        $barang->keterangan = $request->edit_keterangan;
        $barang->kode = $request->edit_kode;
        $barang->hpp = $request->hpp;
        $barang->harga_jual = $request->harga;
        $barang->foto = $imageName;
        $barang->dibuat_oleh = Auth::user()->nama;
        $barang->save();

        $queryString = $request->_query_string ? '?' . $request->_query_string : '';
        $redirectUrl = route('barang.index') . $queryString;

        if ($barang) {
            return redirect()->to($redirectUrl)->with('success', 'Barang berhasil diupdate.');
        } else {
            return redirect()->to($redirectUrl)->with('error', 'Barang gagal diupdate.');
        }
    }

    public function updateStok(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->status = 0;
        $barang->save();
        
        $queryString = $request->_query_string ? '?' . $request->_query_string : '';
        $redirectUrl = route('barang.index') . $queryString;

        if ($barang) {
            return redirect()->to($redirectUrl)->with('success', 'Barang berhasil dihapus.');
        } else {
            return redirect()->to($redirectUrl)->with('error', 'Barang gagal dihapus.');
        }
    }
}
