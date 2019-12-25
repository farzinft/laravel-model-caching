<?php

namespace Fthi\ModelCaching;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/model-caching.php' => config_path('model-caching.php'),
        ]);
    }
}