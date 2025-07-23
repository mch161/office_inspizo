<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Support\Facades\Auth; // Impor Auth untuk mendapatkan user

class BarangObserver
{
    /**
     * Handle the Barang "created" event.
     */
    public function created(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "deleting" event.
     */
    public function deleting(Barang $barang): void
    {
        Stok::where('kd_barang', $barang->kd_barang)->delete();
    }

    /**
     * Handle the Barang "updated" event.
     */
    public function updated(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "deleted" event.
     */
    public function deleted(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "restored" event.
     */
    public function restored(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "force deleted" event.
     */
    public function forceDeleted(Barang $barang): void
    {
        //
    }
}
