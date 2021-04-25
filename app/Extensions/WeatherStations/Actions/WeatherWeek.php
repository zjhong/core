<?php


namespace App\Extensions\WeatherStations\Actions;


use App\Extensions\BaseClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class WeatherWeek
 * @title  24最近7天天气概况
 * @package App\Extensions\WeatherStations
 */
class WeatherWeek extends BaseClass
{
    public function main(array $device_id, array $data, array $fields, bool $initial): array
    {
        $Arr = [];
        /*data[] area 地区名称*/
        $lnt = Arr::get($data, 'message.' . 'logt', 0);
        if(empty($lnt)){
            $lnt = [116.397499,39.908722];
        }
        $AreaName = \App\Facades\AliWeather::getCityName(['logt' => $lnt[0].','.$lnt[1]]);
        $area = ['area' =>$AreaName['area']];
        $res = \App\Facades\AliWeather::DayFifteen($area);
        $Arr['future'] = array_slice($res['showapi_res_body']['dayList'],0,7);
        foreach ($Arr['future'] as $key => $val){
            $Arr['future'][$key]['daytime'] = $this->applyDay(date('w',strtotime($val['daytime'])));
        }
        $Arr['name'] = $AreaName['area'];//地理名称
        return $Arr;
    }

    function applyDay($day){
        if($day == "1" ){
            $str = "星期一";
        }else if( $day == "2" ){
            $str = "星期二";
        }else if( $day == "3" ){
            $str = "星期三";
        }else if( $day == "4" ){
            $str = "星期四";
        }else if( $day == "5" ){
            $str = "星期五";
        }else if( $day == "6" ){
            $str = "星期六";
        }else if( $day == "0" ){
            $str = "星期日";
        }else{
            $str = "数据有误!";
        }
        return $str;
    }
}
