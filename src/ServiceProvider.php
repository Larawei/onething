<?php

namespace Larawei\Onething;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Onething::class, function(){

            // read for config/onething.php

            $app_id  = config('onething.app_id');
            $app_key = config('onething.app_key');
            $salt    = config('onething.salt');
            $request_url = config('onething.request_url');

            return new Onething($app_id, $app_key, $salt, $request_url);
        });

        $this->app->alias(Onething::class, 'onething');
    }

    public function provides()
    {
        return [Onething::class, 'onething'];
    }
}