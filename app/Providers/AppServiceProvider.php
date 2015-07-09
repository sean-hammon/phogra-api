<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Phogra\Response\Warnings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Warnings', function($app)
        {
            return new Warnings();
        });

    }
}
