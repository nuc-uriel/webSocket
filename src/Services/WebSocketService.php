<?php


namespace Nucuriel\WebSocket\Services;


use Nucuriel\Websocket\Events\ReceiveMsg;
use Nucuriel\Websocket\Jobs\SendMsg;

trait WebSocketService
{
    private $binaryTypeBlob = "\x81";
    private $address;
    private $port;
    private $server;
    private $sockets = array();
    private $clients = array();

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function connect()
    {
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($server, $this->address, $this->port);
        socket_listen($server);
        $this->server = $server;
        $this->sockets[] = $this->server;
        return $this;
    }

    public function run()
    {
        while (true) {
            $write = NULL;
            $except = NULL;
            echo "客户端数量: " . count($this->clients) . "\n";
            socket_select($this->sockets, $write, $except, NULL);
            foreach ($this->sockets as $socket) {
                if ($socket === $this->server) {
                    $client = socket_accept($socket);
                    $this->sockets[] = $client;
                    $key = uniqid();
                    $this->clients[$key] = array(
                        'socket' => $client,
                        'build' => false
                    );
                } else {
                    $len = 0;
                    $buffer = "";
                    do {
                        $l = socket_recv($socket, $buf, 1000, 0);
                        $len += $l;
                        $buffer .= $buf;
                    } while ($l == 1000);
                    $key = $this->search($socket);
                    if ($len < 7) {
                        $this->close($key);
                        continue;
                    }
                    if (!$this->clients[$key]['build']){
                        $this->handshake($buffer, $key);
                    }else {
                        $message = $this->decode($buffer);
                        event(new ReceiveMsg($socket, $message));
                    }
                }
            }
        }
        socket_close($this->server);
    }

    private function getKey($buffer)
    {
        $headers = $this->getRequestHeaders($buffer);
        if (array_key_exists("Sec-WebSocket-Key", $headers)) {
            return $headers["Sec-WebSocket-Key"];
        } else {
            return "";
        }
    }

    private function encrypt($buffer)
    {
        $key = $this->getKey($buffer);
        $mask = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
        return base64_encode(sha1($key . $mask, true));
    }

    private function handshake($buffer, $key)
    {
        $acceptKey = $this->encrypt($buffer);
        $upgrade = <<<HEADERS
HTTP/1.1 101 Switching Protocols
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Accept: $acceptKey


HEADERS;
        $socket = $this->clients[$key]['socket'];
        socket_write($socket, $upgrade, strlen($upgrade));
        $this->clients[$key]['build'] = true;
        return true;
    }

    private function search($socket)
    {
        foreach ($this->clients as $key=>$client) {
            if ($client['socket'] === $socket) {
                return $key;
            }
        }
        return false;
    }

    private function getRequestHeaders($buffer)
    {
        $headers = array();
        $raws = explode("\r\n", $buffer);
        foreach ($raws as $raw) {
            if ($raw){
                $item = explode(":", $raw);
                $headers[trim($item[0])] = trim($item[1]);
            }
        }
        return $headers;
    }

    private function close($key)
    {
        $socket = $this->clients[$key]['socket'];
        socket_close($socket);
        unset($this->clients[$key]);
        $this->sockets=array($this->server);
        foreach($this->clients as $v){
            $this->sockets[]=$v['socket'];
        }
    }

    private function decode($buffer){
        $decoded = "";
        $len = ord($buffer[1]) & 127;
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data  = substr($buffer, 8);
        } else {
            if ($len === 127) {
                $masks = substr($buffer, 10, 4);
                $data  = substr($buffer, 14);
            } else {
                $masks = substr($buffer, 2, 4);
                $data  = substr($buffer, 6);
            }
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    private function encode($buffer)
    {
        $len = strlen($buffer);
        $first_byte = $this->binaryTypeBlob;
        if ($len <= 125) {
            $encode_buffer = $first_byte . chr($len) . $buffer;
        } else {
            if ($len <= 65535) {
                $encode_buffer = $first_byte . chr(126) . pack("n", $len) . $buffer;
            } else {
                //pack("xxxN", $len)pack函数只处理2的32次方大小的文件，实际上2的32次方已经4G了。
                $encode_buffer = $first_byte . chr(127) . pack("xxxxN", $len) . $buffer;
            }
        }
        return $encode_buffer;
    }

    public function sendMsg($socket, $message)
    {
        $response = $this->encode($message);
        $job = (new sendMsg($socket, $response))->onQueue('webSocket');
        dispatch($job);
    }
}