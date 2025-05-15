<?php

namespace Nasimic\Payriff;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\ServiceProvider;
use Nasimic\Payriff\Exceptions\ConfigurationNotFoundException;
use Nasimic\Payriff\Exceptions\EmptySecretKeyException;

class PayriffServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/payriff.php' => config_path('payriff.php'),
        ], 'payriff-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     * @throws ConfigurationNotFoundException
     * @throws EmptySecretKeyException
     */
    public function register(): void
    {
        $this->app->singleton(PayriffClient::class, function ($app){
            if(! $app["config"]->get('payriff')){
                throw new ConfigurationNotFoundException;
            }

            if (is_null($app['config']->get('payriff.secret_key'))) {
                throw new EmptySecretKeyException;
            }

            return new PayriffClient(
                $app['config']->get('payriff.secret_key'),
                $app->make(HttpClient::class)
            );
        });
    }


}
