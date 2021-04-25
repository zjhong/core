<?php


namespace App\Extensions\WeatherStations\Actions;


use App\Extensions\BaseClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class AirQuality
 * @title 空气质量
 * @package App\Extensions\WeatherStations
 */
class AirQuality extends BaseClass
{
    /**
     * @param array $device_id
     * @param array $data
     * @param array $fields
     * @param bool $initial
     * @return array
     */
    public function main(array $device_id, array $data, array $fields, bool $initial): array
    {
//        Log::debug('AirQuality', [$device_id, $data, $fields]);
        $lnt = Arr::get($data, 'message.' . 'logt', 0);
        if(empty($lnt)){
            $lnt = [116.397499,39.908722];
        }
        $Arr['sunrise'] = date_sunset(time(),SUNFUNCS_RET_STRING,$lnt[1],$lnt[0]);//日落
        $Arr['sunset'] = date_sunrise(time(),SUNFUNCS_RET_STRING,$lnt[1],$lnt[0]);//日出
        /*data[] from:输入的坐标类型  lat 纬度值  lnt 经度值*/
        $parameter = ['from' => '5','lat' => $lnt[1], 'lng' =>$lnt[0]];
        $res = \App\Facades\AliWeather::GpsToWeather($parameter);
        $Arr['now'] = $res['showapi_res_body']['now'];
        $Arr['now']['aqiDetail']['nai'] = '0';//特殊处理
        $Arr['now']['aqiDetail']['co2'] = '0';//特殊处理
        return $Arr;
    }
}
