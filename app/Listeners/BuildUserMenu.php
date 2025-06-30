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
        if (Auth::check()) {
            $event->menu->addAfter('keuangan', [
                'header' => 'ACCOUNT_SETTINGS'
            ]);

            $event->menu->addAfter('account_settings', [
                'text' => Auth::user()->name,
                'icon' => 'fas fa-fw fa-user',
                'submenu' => [
                    [
                        'text' => 'profile',
                        'url' => 'admin/settings',
                        'icon' => 'fas fa-fw fa-user',
                    ],
                    [
                        'text' => 'change_password',
                        'url' => 'admin/settings',
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