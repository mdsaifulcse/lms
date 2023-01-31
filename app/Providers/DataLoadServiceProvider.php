<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DataLoadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('dataload',function (){
            return new \App\CustomFacades\DataLoadController;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
