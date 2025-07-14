<?php

namespace App\Providers;

use App\Models\Karyawan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Listeners\BuildUserMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Karyawan::observe(\App\Observers\KaryawanObserver::class);

        Gate::define('access', function ($user = null) {
            return Auth::guard('pelanggan')->check() || Auth::guard('karyawan')->check();
        });

        Gate::define('access-karyawan', function ($user = null) {
            return Auth::guard('karyawan')->check();
        });
        Gate::define('superadmin', function ($user = null) {
            return Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->role == 'superadmin';
        });
    }
}

