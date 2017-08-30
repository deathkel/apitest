<?php

namespace Deathkel\Apitest;

use Illuminate\Support\ServiceProvider;

class ApiTestServiceProvider extends ServiceProvider
{

    /**
     * boot process
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/frontend/static' => base_path('public/api'),
            __DIR__ . '/frontend/blade/index.blade.php' => base_path('resources/views/api/index.blade.php'),
            __DIR__ . '/ApiTestController.php' => base_path('app/Http/Controllers/ApiTestController.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [

        ];
    }
}
