<?php

namespace App\Package;
use Illuminate\Support\ServiceProvider;

class TapServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/tap_payment.php' => config_path('tap_payment.php'),
        ], 'tap_payment-config');

    }

    public function register()
    {
        $packageConfigFile = __DIR__.'/../config/tap_payment.php';

        $this->mergeConfigFrom(
            $packageConfigFile, 'tap_payment'
        );

    }

    protected function registerBindings()
    {
        $this->app->singleton(Payment::class, function () {
            return new Payment();
        });

        $this->app->alias(Payment::class, 'Payment');
    }
}
