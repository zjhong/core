<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CoordinateTransform
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
            if (isset($data['values']['longitude']) && $data['values']['latitude']) {
                switch (config('coordinate.from')) {
                    case 'WGS-84':
                        list($longitude, $latitude) = \App\Facades\CoordinateTransform::wgs84togcj02($data['values']['longitude'], $data['values']['latitude']);
                        $data['values']['longitude'] = $longitude;
                        $data['values']['latitude'] = $latitude;
                        $event->data = json_encode($data);
                        Log::info('GPS Convert to', [$longitude, $latitude]);
                        break;
                    default:
                        break;
                }
            }
        } catch (\Exception $e) {
            Log::error('CoordinateTransform handle error', [$e->getCode(), $e->getMessage()]);
        }
    }
}
