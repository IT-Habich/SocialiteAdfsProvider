<?php

namespace CodeAdminDe\SocialiteAdfsProvider;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap Socialite Adfs Provider.
     */
    public function boot()
    {
        $socialite = $this->app->make(Factory::class);
        $socialite->extend(
            'Adfs',
            function ($app) use ($socialite) {
                $config = $app['config']['services.Adfs'];

                return $socialite->buildProvider(Provider::class, $config);
            }
        );
    }
}
