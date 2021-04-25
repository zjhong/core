<?php


namespace App\Extensions\WeatherStations\Actions;


use App\Extensions\BaseClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class EnvironmentalParameters
 * @title 环境参数
 * @package App\Extensions\WeatherStations
 */
class EnvironmentalParameters extends BaseClass
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
//        Log::debug('EnvironmentalParameters', [$device_id, $data, $fields]);
        /*data[] area 地区名称  needAlarm 天气预警1为返回，0为不返回   needIndex 指数数据1为返回，0为不返回 needMoreDay 是否需要返回7天数据中的后4天。1为返回，0为不返回*/
        $lnt = Arr::get($data, 'message.' . 'logt', 0);
        if(empty($lnt)) {
            $lnt = [116.397499, 39.908722];
        }
        $AreaName = \App\Facades\AliWeather::getCityName(['logt' => $lnt[0].','.$lnt[1]]);
        $area = ['area' =>$AreaName['area']];
        $res = \App\Facades\AliWeather::AreaToWeather($area);
        $arr['temperature'] = $res['showapi_res_body']['now']['temperature'];
        $arr['sd'] = $res['showapi_res_body']['now']['sd'];
        $arr['weather'] = $res['showapi_res_body']['now']['weather'];
        $arr['weather_pic'] = $res['showapi_res_body']['now']['weather_pic'];
        $arr['wind_direction'] = $res['showapi_res_body']['now']['wind_direction'];
        $arr['wind_power'] = $res['showapi_res_body']['now']['wind_power'];
        $arr['f1'] = $res['showapi_res_body']['f1'];
        $arr['name'] = $AreaName['area'];//地理名称
        return $arr;
    }
}
