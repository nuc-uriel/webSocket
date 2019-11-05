<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 21:03
 */

namespace Nucuriel\Websocket\Listeners;


use Nucuriel\WebSocket\Contracts\WSListener;

class DefaultListener extends WSListener
{

    public function dispose($msg)
    {
        // TODO: Implement dispose() method.
        $response = "我收到你发的消息了，你发的是：" . $msg;
        WebSocket::sendMsg($this->socket, $response);
    }
}