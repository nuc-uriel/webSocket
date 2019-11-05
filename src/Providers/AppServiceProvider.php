<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 21:30
 */

namespace Nucuriel\Websocket\Providers;

use Illuminate\Support\ServiceProvider;
use Nucuriel\WebSocket\WebSocket;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('websocket', function ($app) {
            return new Websocket();
        });
    }
}