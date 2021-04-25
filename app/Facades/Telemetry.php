<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Extension
 * @method static handle($data)
 * @method static sendDataToClient($token, array $data)
 * @method static sendDataToClientByDeviceId($device_id, array $data)
 * @method static sendDataToTCPClient($token, array $data)
 * @method static sendDataToTCPClientByDeviceId($device_id, array $data)
 * @method static microtime()
 * @package App\Facades
 */
class Telemetry extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '\App\Services\TelemetryService';
    }
}
