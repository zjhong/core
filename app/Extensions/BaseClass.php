<?php

namespace App\Extensions;

use App\Models\Telemetry\Kv;
use Illuminate\Support\Facades\Log;

abstract class BaseClass implements BaseInterface
{
    const WIDGET_TYPE_PANEL = 'panel';
    const FIELD_TYPE_CHART = 1; //chart
    const FIELD_TYPE_SWITCH = 2; //switch
    const FIELD_TYPE_SCROLL = 3; //scroll
    const FIELD_TYPE_STATUS = 4; //control status
    const FIELD_TYPE_LIQUID = 5; //Liquid level status
    const FIELD_TYPE_ADDRESS = 6; //ADDRESS
    const FIELD_TYPE_VISUAL = 7; //VISUAL

    /**
     * main function
     * @param array $device_id
     * @param array $data
     * @param array $fields
     * @param bool $initial
     * @return array data
     */
    public function main(array $device_id, array $data, array $fields, bool $initial): array
    {
        try {
            $output = Kv::getTelemetryData($device_id, $data['config']['latestTime'], intval($data['config']['startTs']), intval($data['config']['endTs']), $data['config']['operator'], $fields);
        } catch (\Exception $e) {
            Log::error('getTelemetryData', [
                $e->getCode(),
                $e->getMessage()
            ]);
            $output = [
                'error' => $e->getMessage()
            ];
        }
        return $output;
    }
}
