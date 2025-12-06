<?php

namespace App\Listeners;

use App\Models\Izin;
use App\Models\Pesanan;
use App\Models\PresensiLembur;
use App\Models\SuratPerintahKerja;
use App\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class BuildUserMenu
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BuildingMenu $event)
    {
        if (Auth::guard('karyawan')->check()) {

            $user = Auth::guard('karyawan')->user();
            $userName = $user->username;
            $userPhoto = $user->foto;
            $imgSrc = $userPhoto
                ? asset('storage/' . $userPhoto)
                : asset('default.png');

            $imgTag = '<img src="' . $imgSrc . '" class="img-circle" alt="User Image" style="width: 28px; height: 28px; margin-right: 5px;">';

            $menuText = new HtmlString($imgTag . $userName);

            $event->menu->add([
                'header' => 'ACCOUNT_SETTINGS'
            ]);

            $event->menu->add([
                'text' => $menuText,
                'icon' => '',
                'submenu' => [
                    [
                        'text' => 'Profile',
                        'route' => 'profile',
                        'icon' => 'fas fa-fw fa-user',
                    ],
                    [
                        'text' => 'logout',
                        'route' => 'logout',
                        'icon' => 'fas fa-fw fa-sign-out-alt'
                    ],
                ],
            ]);
        }

        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->role == 'superadmin') {
            $event->menu->add([
                'text' => 'Manage Users',
                'route' => 'users.index',
                'icon' => 'fas fa-fw fa-users',
            ]);
        }
        $pesananYangBelumDisetujui = $this->getPesananYangBelumDisetujui();
        $suratPerintahBelumSelesai = $this->getSuratPerintahBelumSelesai();
        $event->menu->addAfter('pelanggan', [
            'key' => 'tiket',
            'text' => 'Tiket',
            'icon' => 'fas fa-fw fa-calendar-alt',
            'can' => 'access-karyawan',
            'label' => $pesananYangBelumDisetujui + $suratPerintahBelumSelesai > 0 ? $pesananYangBelumDisetujui + $suratPerintahBelumSelesai : '',
            'label_color' => $pesananYangBelumDisetujui + $suratPerintahBelumSelesai > 0 ? 'danger' : '',
            'submenu' => [
                [
                    'text' => 'Tiket',
                    'route' => 'tiket.index',
                    'icon' => 'fa fas fa-clipboard-list',
                    'label' => $pesananYangBelumDisetujui > 0 ? $pesananYangBelumDisetujui : '',
                    'label_color' => $pesananYangBelumDisetujui > 0 ? 'danger' : '',
                    'can' => 'access-karyawan',
                    'active' => [
                        'tiket/*',
                    ],
                ],
                [
                    'text' => 'Kalender',
                    'icon' => 'fa fas fa-calendar-alt',
                    'route' => 'agenda.index',
                    'can' => 'access-karyawan'
                ],
                [
                    'text' => 'Surat Perintah',
                    'icon' => 'fas fa-fw fa-scroll',
                    'can' => 'access-karyawan',
                    'route' => 'surat-perintah.index',
                    'label' => $suratPerintahBelumSelesai > 0 ? $suratPerintahBelumSelesai : '',
                    'label_color' => $suratPerintahBelumSelesai > 0 ? 'danger' : '',
                    'active' => ['surat-perintah*']
                ],
                // [
                //     'text' => 'Data Kunjungan',
                //     'route' => 'kunjungan.index',
                //     'icon' => 'fas fa-fw fa-newspaper',
                //     'can' => 'access-karyawan'
                // ]
            ],
        ]);
        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->role == 'superadmin') {
            $izin = $this->getIzin();
            $lembur = $this->getLembur();
        } else {
            $izin = 0;
            $lembur = 0;
        }

        $event->menu->addAfter('tiket', [
            'text' => 'Presensi',
            'icon' => 'fas fa-fw fa-address-card',
            'can' => 'access-karyawan',
            'label' => $izin + $lembur > 0 ? $izin + $lembur : '',
            'label_color' => $izin + $lembur > 0 ? 'danger' : '',
            'submenu' => [
                [
                    'text' => 'Fingerprint',
                    'route' => 'presensi.index',
                    'icon' => 'fa fas fa-fingerprint',
                    'can' => 'access-karyawan',
                    'active' => [
                        'presensi',
                        'presensi/view',
                    ]
                ],
                [
                    'text' => 'Izin',
                    'icon' => 'fa fas fa-envelope-open',
                    'route' => 'izin.index',
                    'can' => 'access-karyawan',
                    'label' => $izin > 0 ? $izin : '',
                    'label_color' => $izin > 0 ? 'danger' : '',
                    'active' => ['izin*']
                ],
                [
                    'text' => 'Lembur',
                    'route' => 'lembur.index',
                    'icon' => 'fa fas fa-clipboard-list',
                    'can' => 'access-karyawan',
                    'label' => $lembur > 0 ? $lembur : '',
                    'label_color' => $lembur > 0 ? 'danger' : '',
                    'active' => ['form/lembur*']
                ],
                [
                    'text' => 'Hari Libur',
                    'route' => 'libur.index',
                    'icon' => 'fa fas fa-calendar-alt',
                    'can' => 'access-karyawan',
                ],
                [
                    'text' => 'Rekap Bulanan',
                    'route' => 'presensi.bulanan',
                    'icon' => 'fa fas fa-calendar',
                    'can' => 'access-karyawan',
                ]
            ]
        ]);
    }

    private function getPesananYangBelumDisetujui()
    {
        return count(Pesanan::where('progres', '1')->where('status', '=', '0')->get());
    }

    private function getSuratPerintahBelumSelesai()
    {
        return count(SuratPerintahKerja::whereNotNull('kd_karyawan')->where('kd_karyawan', Auth::user()->kd_karyawan ?? null)->where('status', '0')->get());
    }

    private function getIzin()
    {
        return count(Izin::where('status', '0')->get());
    }

    private function getLembur()
    {
        return count(PresensiLembur::where('verifikasi', '0')->get());
    }
}