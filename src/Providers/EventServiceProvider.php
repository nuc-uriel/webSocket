<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 21:32
 */

namespace Nucuriel\Websocket\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Nucuriel\Websocket\Events\ReceiveMsg' => [
            'Nucuriel\Websocket\Listeners\DefaultListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}