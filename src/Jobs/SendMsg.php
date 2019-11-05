<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/11/5 0005
 * Time: 21:18
 */

namespace Nucuriel\Websocket\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMsg implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $socket;
    protected $message;

    /**
     * Create a new job instance.
     * @param string $view
     * @param array $parameter
     * @param string $to
     * @param string $subject
     * @return void
     */
    public function __construct($socket, $message)
    {
        $this->socket = $socket;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        socket_write($this->socket, $this->message, strlen($this->message));
    }
}