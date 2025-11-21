<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Auth; 
use App\Models\Profile;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use Carbon\Carbon;


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
     // Share profile company ke semua view
        View::composer('*', function ($view) {
            // Ambil data profile perusahaan terbaru
            $profile = Profile::latest()->first();
            $view->with('profile', $profile);
        });

    // Bikin variabel global untuk semua view
    View::composer('*', function ($view) {
        $view->with('totalUsers', User::count())
             ->with('totalRoles', Role::count())
             ->with('totalMenus', Menu::count());
        $onlineUsers = User::where('last_activity', '>=', Carbon::now()->subMinutes(5))->count();
        $view->with('onlineUsers', $onlineUsers);
    });
}
}
