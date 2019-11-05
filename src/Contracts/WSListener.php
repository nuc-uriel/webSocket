<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 20:33
 */

namespace Nucuriel\WebSocket\Contracts;


use Nucuriel\Websocket\Events\ReceiveMsg;

abstract class WSListener
{
    protected $event;
    protected $socket;
    protected $message;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReceiveMsg  $event
     * @return void
     */
    public function handle(ReceiveMsg $event)
    {
        $this->event = $event;
        $this->socket = $event->socket;
        $this->message = $event->message;
        $this->dispose($this->message);
    }

    abstract public function dispose($msg);
}