<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use App\Models\SchoolProfile;

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
        view()->composer('*', function ($view) {
            try {
                $profile = SchoolProfile::firstOrCreate(['id' => 1]);
                if (app()->environment('testing')) {
                    \Log::info('VIEW COMPOSER SCHOOL PROFILE: ' . json_encode($profile->toArray()));
                }
                $view->with('school_profile', $profile);
            } catch (\Exception $e) {
                if (app()->environment('testing')) {
                    throw $e;
                }
                // Table doesn't exist yet during migrations
            }
        });
    }
}
