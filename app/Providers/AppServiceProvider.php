<?php

namespace App\Providers;

use App\Services\StorageDocumentManager;
use Illuminate\Support\ServiceProvider;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
