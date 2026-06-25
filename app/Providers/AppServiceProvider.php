<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- Tambahkan ini
use App\Models\User; // <-- Tambahkan ini

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
        // Memberi tahu Laravel syarat untuk menjadi 'admin'
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}