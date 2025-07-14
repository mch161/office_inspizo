<?php

namespace App\Observers;

use App\Models\Jurnal;
use App\Models\Karyawan;

class KaryawanObserver
{
    /**
     * Handle the Karyawan "created" event.
     */
    public function created(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "updated" event.
     */
    public function updated(Karyawan $karyawan): void
    {
        if ($karyawan->isDirty('nama')) {
            Jurnal::where('kd_karyawan', $karyawan->kd_karyawan)
                ->update(['dibuat_oleh' => $karyawan->nama]);
        }
    }

    /**
     * Handle the Karyawan "deleted" event.
     */
    public function deleted(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "restored" event.
     */
    public function restored(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "force deleted" event.
     */
    public function forceDeleted(Karyawan $karyawan): void
    {
        //
    }
}
