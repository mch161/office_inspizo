<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\Stok;

class BarangObserver
{
    /**
     * Handle the Barang "created" event.
     */
    public function created(Barang $barang): void
    {
    // Create a new Stok entry when a Barang is created
    $stok = new Stok();
    $stok->kd_barang = $barang->kd_barang;
    $stok->stok = $barang->stok;
    $stok->save();

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
