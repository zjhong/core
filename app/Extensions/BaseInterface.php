<?php

namespace App\Extensions;

interface BaseInterface
{
    /**
     * Processing telemetry data
     * @param array $device_id device id
     * @param array $data websocket request json data: {"aid":1,"bid":2,"wid":3,"config":{"startTs":1601348753000,"endTs":1601348753000,"operator":"AVG","interval":1000},"data":"any string"}, The one-dimensional key fixed parameter cannot be deleted, and the data parameter value comes from the custom data of each chart component
     * @param array $fields The name of the field defined by the current chart
     * @param bool $initial The default is false, and it is automatically true during initialization or configuration changes
     * @return array return data
     */
    public function main(array $device_id, array $data, array $fields, bool $initial): array;
}
