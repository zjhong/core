<?php

namespace App\Events;

use App\Facades\Extension;
use Hhxsv5\LaravelS\Swoole\Events\WorkerStartInterface;
use Swoole\Http\Server;

class WorkerStartEvent implements WorkerStartInterface
{
    public function __construct()
    {
    }

    public function handle(Server $server, $workerId)
    {
        Extension::init();
    }
}
