<?php


use Illuminate\Console\Command;
use Nucuriel\WebSocket\Services\WebSocketService;

class WebSocketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ws:socket {--address} {--port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '启动websocket服务器类';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(WebSocketService $webSocketService)
    {
        $address = $this->option('address') ?? config("websocket.address");
        $port = $this->option('port') ?? config("websocket.port");
        $webSocketService->setAddress($address)->setPort($port)->connect()->run();
    }
}