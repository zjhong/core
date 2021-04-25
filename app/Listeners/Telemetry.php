<?php

namespace App\Listeners;

use App\Models\Telemetry\Kv;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * default Telemetry listener
 * @package App\Listeners
 */
class Telemetry
{
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
     * @param \App\Events\Telemetry $event
     * @return void
     */
    public function handle(\App\Events\Telemetry $event)
    {
        try {
            $data = json_decode($event->data, true);
            Log::debug('Telemetry handle data', ['data' => $event->data]);
            if (isset($data['token'])) {
                Kv::insertByToken($data['token'], $data['values'], (isset($data['ts']) && intval($data['ts']) > 0) ? $data['ts'] : \App\Facades\Telemetry::microtime());
            } else {
                Log::warning('Telemetry token missing: ' . $event->data);
            }
        } catch (\Exception $e) {
            Log::error('Telemetry handle error', [$e->getCode(), $e->getMessage()]);
        }
    }
}
