<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('userImgPHolder', asset('storage/avatar_placeholder.png'));
        View::share('articleImgPHolder', asset('storage/thumbnail_placeholder.png'));
        
        View::composer('partials.navbar', function ($view) {
            $newNotifications = false;

            if (Auth::check())
            {
                $newNotifications = Auth::user()->notifications
                    ->where('is_read', false)->count() > 0;
            }

            $view->with('newNotifications', $newNotifications);
        });
    }
}
