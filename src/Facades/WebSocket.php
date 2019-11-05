<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 21:27
 */

namespace Nucuriel\Websocket\Facades;


use Illuminate\Support\Facades\Facade;

class WebSocket
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'websocket';
    }
}