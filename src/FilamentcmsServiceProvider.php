<?php

namespace JoaoCastro\FilamentCms;

use Illuminate\Support\ServiceProvider;

class FilamentcmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filamentcms-migrations');

        $this->loadMigrationsFrom([
            __DIR__.'/../database/migrations',
        ]);
    }

    public function register()
    {

    }
}