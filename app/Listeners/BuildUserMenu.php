<?php

namespace App\Listeners;

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
                : asset('storage/profile/default.png');

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
    }
}
