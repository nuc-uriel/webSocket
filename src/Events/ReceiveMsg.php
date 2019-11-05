<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 20:46
 */

namespace Nucuriel\Websocket\Events;

use Illuminate\Queue\SerializesModels;

class ReceiveMsg
{
    use SerializesModels;

    public $socket;
    public $message;
    public function __construct($socket, $message)
    {
        $this->socket = $socket;
        $this->message = $message;
    }
}