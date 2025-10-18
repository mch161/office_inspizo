<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\Stok;
use Illuminate\Support\Facades\URL;
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

        Barang::observe(\App\Observers\BarangObserver::class);
        Stok::observe(\App\Observers\StokObserver::class);

        Karyawan::observe(\App\Observers\KaryawanObserver::class);

        Gate::define('access-karyawan', function ($user = null) {
            return Auth::guard('karyawan')->check();
        });

        // Gate untuk hak akses SUPERADMIN.
        Gate::define('superadmin', function ($user = null) {
            if (!Auth::guard('karyawan')->check()) {
                return false;
            }
            // Akan true HANYA JIKA: rolenya 'superadmin' DAN TIDAK sedang dalam mode 'view_as_karyawan'.
            return Auth::guard('karyawan')->user()->role == 'superadmin' && !session('view_as_karyawan');
        });

        Gate::define('session-view-as-karyawan', function ($user = null) {
            return session('view_as_karyawan');
        });

        // Gate untuk hak akses KARYAWAN BIASA.
        Gate::define('only-karyawan', function ($user = null) {
            if (!Auth::guard('karyawan')->check()) {
                return false;
            }
            $authedUser = Auth::guard('karyawan')->user();

            // Akan true jika rolenya BUKAN 'superadmin'.
            if ($authedUser->role !== 'superadmin') {
                return true;
            }

            // Akan true juga jika rolenya 'superadmin' TETAPI sedang dalam mode 'view_as_karyawan'.
            if ($authedUser->role === 'superadmin' && session('view_as_karyawan')) {
                return true;
            }

            return false;
        });
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

