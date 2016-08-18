<?php

namespace Akibatech\Ovhsms;

use Akibatech\Ovhsms\OvhSms;

/**
 * Class ServiceProvider
 *
 * @package Akibatech\Ovhsms
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @var string
     */
    protected $configName = 'laravel-ovh-sms';

    //-------------------------------------------------------------------------

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/' . $this->configName . '.php';

        $this->mergeConfigFrom($configPath, $this->configName);

        $this->app->bind(OvhSms::class, OvhSms::class);

        $this->app->singleton('ovhsms', function ($app)
        {
            return new OvhSms();
        });

        $this->app->alias('ovhsms', OvhSms::class);
    }

    //-------------------------------------------------------------------------

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/' . $this->configName . '.php';

        $this->publishes([$configPath => config_path($this->configName . '.php')], 'config');
    }

    //-------------------------------------------------------------------------
}