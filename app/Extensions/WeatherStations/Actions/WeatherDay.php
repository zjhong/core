<?php


namespace App\Extensions\WeatherStations\Actions;


use App\Extensions\BaseClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class WeatherDay
 * @title  24小时天气概况
 * @package App\Extensions\WeatherStations
 */
class WeatherDay extends BaseClass
{
    public function main(array $device_id, array $data, array $fields, bool $initial): array
    {
//        Log::debug('WeatherDay', [$device_id, $data, $fields]);
        /*data[] area : 地区名称*/
        $lnt = Arr::get($data, 'message.' . 'logt', 0);
        if(empty($lnt)){
            $lnt = [116.397499,39.908722];
        }
        $AreaName = \App\Facades\AliWeather::getCityName(['logt' => $lnt[0].','.$lnt[1]]);
        $area = ['area' =>$AreaName['area']];
        $res = \App\Facades\AliWeather::hour24($area);
        foreach ($res['showapi_res_body']['hourList'] as $key => $val){
            $res['showapi_res_body']['hourList'][$key]['time'] = date('H:i',strtotime($val['time']));
        }
        $Arr = $res['showapi_res_body']['hourList'];
        return $Arr;
    }
}
