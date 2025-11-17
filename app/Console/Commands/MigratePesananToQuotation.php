<?php

namespace App\Console\Commands;

use App\Models\Tiket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use App\Models\PesananBarang;
use App\Models\PesananJasa;
use App\Models\Quotation;
use App\Models\QuotationItem;

class MigratePesananToQuotation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-pesanan-to-quotation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from pesanan, pesanan_barang, and pesanan_jasa to quotation and quotation_item tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data dari Pesanan ke Quotation...');

        DB::transaction(function () {
            Pesanan::with('details')->chunk(100, function ($pesanans) {
                foreach ($pesanans as $pesanan) {
                    
                    if (is_null($pesanan->kd_tiket)) {
                        $tiket = Tiket::create([
                            'prioritas' => 1,
                            'jenis' => 'Pesanan',
                            'deskripsi' => $pesanan->deskripsi_pesanan,
                            'kd_pelanggan' => $pesanan->kd_pelanggan,
                            'via' => 'web',
                        ]);
                        $pesanan->jenis = 'Quotation';
                        $pesanan->kd_tiket = $tiket->kd_tiket;
                        $pesanan->save();
                    }
                    
                    $quotation = Quotation::create([
                        'kd_tiket' => $pesanan->kd_tiket,
                        'kd_pelanggan' => $pesanan->kd_pelanggan,
                        'tanggal' => $pesanan->tanggal,
                        'dibuat_oleh' => $pesanan->dibuat_oleh,
                        'created_at' => $pesanan->created_at,
                        'updated_at' => $pesanan->updated_at,
                    ]);

                    $this->line("Migrasi Pesanan {$pesanan->kd_pesanan} -> Quotation {$quotation->kd_quotation}");

                    foreach ($pesanan->details as $detail) {

                        $barangItems = PesananBarang::where('kd_pesanan_detail', $detail->kd_pesanan_detail)->get();
                        foreach ($barangItems as $item) {
                            QuotationItem::create([
                                'kd_quotation' => $quotation->kd_quotation,
                                'kd_barang' => $item->kd_barang,
                                'kd_jasa' => null,
                                'harga' => $item->harga_jual,
                                'jumlah' => $item->jumlah,
                                'subtotal' => $item->subtotal,
                                'created_at' => $item->created_at ?? now(),
                                'updated_at' => $item->updated_at ?? now(),
                            ]);
                            $this->line("  -> Migrasi Barang: {$item->nama_barang}");
                        }

                        $jasaItems = PesananJasa::where('kd_pesanan_detail', $detail->kd_pesanan_detail)->get();
                        foreach ($jasaItems as $item) {
                                                        
                            QuotationItem::create([
                                'kd_quotation' => $quotation->kd_quotation,
                                'kd_barang' => null,
                                'kd_jasa' => $item->kd_jasa,
                                'harga' => $item->harga_jasa,
                                'jumlah' => $item->jumlah,
                                'subtotal' => $item->subtotal,
                                'created_at' => $item->created_at ?? now(),
                                'updated_at' => $item->updated_at ?? now(),
                            ]);
                            $this->line("  -> Migrasi Jasa: {$item->nama_jasa}");
                        }
                    }
                }
            });
        });

        $this->info('Migrasi data selesai.');
    }
}