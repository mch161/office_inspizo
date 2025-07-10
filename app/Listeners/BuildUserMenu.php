<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
        $user = null;
        $userName = null;

        if (Auth::guard('karyawan')->check()) {
            $user = Auth::guard('karyawan')->user();
            $userName = $user->username;
        }
        elseif (Auth::guard('pelanggan')->check()) {
            $user = Auth::guard('pelanggan')->user();
            $userName = $user->username;
        }

        if ($user) {
            $event->menu->add([
                'header' => 'ACCOUNT_SETTINGS'
            ]);

            $event->menu->add([
                'text' => $userName,
                'icon' => 'fas fa-fw fa-user',
                'submenu' => [
                    [
                        'text' => 'change_password',
                        'route' => 'changepassword',
                        'icon' => 'fas fa-fw fa-lock',
                    ],
                    [
                        'text' => 'logout',
                        'route' => 'logout',
                        'icon' => 'fas fa-fw fa-sign-out-alt'
                    ],
                ],
            ]);
        }
    }
}