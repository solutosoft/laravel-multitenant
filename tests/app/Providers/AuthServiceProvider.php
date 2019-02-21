<?php

namespace Soluto\MultiTenant\Tests\Providers;

use Illuminate\Support\ServiceProvider;
use Soluto\MultiTenant\Providers\UserProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->provider('tenant', function($name, $config)
        {
            return new UserProvider($this->app->hash, $config['model']);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
