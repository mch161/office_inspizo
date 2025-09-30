<?php

namespace App\Listeners;

use App\Models\Pesanan;
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
        $event->menu->addAfter('pelanggan', [
            'text' => 'Agenda & Pesanan',
            'icon' => 'fas fa-fw fa-calendar-alt',
            'can' => 'access-karyawan',
            'label' => $pesananYangBelumDisetujui > 0 ? $pesananYangBelumDisetujui : '',
            'label_color' => $pesananYangBelumDisetujui > 0 ? 'danger' : '',
            'submenu' => [
                [
                    'text' => 'Agenda',
                    'icon' => 'fa fas fa-calendar-alt',
                    'route' => 'agenda.index',
                    'can' => 'access-karyawan'
                ],
                [
                    'text' => 'Pesanan',
                    'route' => 'pesanan.index',
                    'icon' => 'fa fas fa-clipboard-list',
                    'can' => 'access-karyawan',
                    'active' => [
                        'pesanan/*/*',
                    ],
                ],
                [
                    'text' => 'Pesanan yang belum disetujui',
                    'route' => 'pesanan.permintaan',
                    'icon' => 'fa fas fa-clipboard-list',
                    'label' => $pesananYangBelumDisetujui > 0 ? $pesananYangBelumDisetujui : '',
                    'label_color' => $pesananYangBelumDisetujui > 0 ? 'danger' : '',
                    'can' => 'access-karyawan',
                ],
                [
                    'text' => 'Project',
                    'icon' => 'fas fa-fw fa-project-diagram',
                    'can' => 'access-karyawan',
                    'route' => 'project.index'
                ],
                [
                    'text' => 'Surat Perintah',
                    'icon' => 'fas fa-fw fa-scroll',
                    'can' => 'access-karyawan',
                    'route' => 'surat-perintah.index',
                    'active' => ['surat-perintah*']
                ],
            ],
        ]);
    }

    private function getPesananYangBelumDisetujui()
    {
        return count(Pesanan::where('progres', '1')->where('status', '=', '0')->get());
    }
}