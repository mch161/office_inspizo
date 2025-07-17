<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\Stok;

class StokObserver
{
    /**
     * Handle the Stok "created" event.
     */
    public function created(Stok $stok): void
    {
        if ($stok->wasRecentlyCreated) {
            $barang = Barang::findOrFail($stok->kd_barang);
            $barang->stok += $stok->stok_masuk;
            $barang->stok -= $stok->stok_keluar;
            $barang->save();
        }
    }

    /**
     * Handle the Stok "updated" event.
     */
    public function updated(Stok $stok): void
    {
        //
    }

    /**
     * Handle the Stok "deleted" event.
     */
    public function deleted(Stok $stok): void
    {
        $barang = Barang::find($stok->kd_barang);
        if ($barang) {
            $barang->stok -= $stok->stok_masuk;
            $barang->stok += $stok->stok_keluar;
            $barang->save();
        }
    }

    /**
     * Handle the Stok "restored" event.
     */
    public function restored(Stok $stok): void
    {
        //
    }

    /**
     * Handle the Stok "force deleted" event.
     */
    public function forceDeleted(Stok $stok): void
    {
        //
    }
}
