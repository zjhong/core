<?php


namespace App\Http\Controllers\Api\AliWeather;


use App\Http\Controllers\Api\Controller;

/**
 *
 * 以下方法 data 为部分参数 其他参数参考 https://market.aliyun.com/products/57096001/cmapi010812.html?spm=5176.730005-56956004-57096001.productlist.d_cmapi010812.167a250daP3i4F&innerSource=search#sku=yuncode481200008
 * 返回数据 参考同上
 */
class AliWeatherController extends Controller
{
    /**
     * 地名查询天气
     * @return array
     */
    public function AreaToWeather(){
        $data = [
            'area' => '银川市',//地区名称
            'needAlarm' => '1',//天气预警1为返回，0为不返回。
            'needIndex' => '0',//指数数据1为返回，0为不返回。
            'needMoreDay' => '0',//是否需要返回7天数据中的后4天。1为返回，0为不返回。
        ];
        $res = \App\Facades\AliWeather::AreaToWeather($data);
        return $res;
    }

    /**
     * 经纬度查询天气
     * @return array
     */
    public function GpsToWeather(){
        $data = [
            'from' => '5',//输入的坐标类型
            'lat' => '40.242266',//纬度值
            'lng' => '116.2278',//经度值
        ];
        $res = \App\Facades\AliWeather::GpsToWeather($data);
        return $res;
    }

    /**
     * 景点名称查询天气
     * @return array
     */
    public function SpotToWeather(){
        $data = [
            'area' => '丽江',//景点名称
        ];
        $res = \App\Facades\AliWeather::SpotToWeather($data);
        return $res;
    }

    /**
     * 区号邮编查询天气
     * @return array
     */
    public function PhonePostCodeWeather(){
        $data = [
            'phone_code' => '021',//电话区号，比如上海021 注意邮编和区号必须二选一输入。都输入时，以邮编为准。
            'post_code' => '200000',//邮编，比如上海200000
        ];
        $res = \App\Facades\AliWeather::PhonePostCodeWeather($data);
        return $res;
    }

    /**
     * IP查询天气
     * @return array
     */
    public function IpToWeather(){
        $data = [
            'ip' => '223.5.5.5',//用户ip。
        ];
        $res = \App\Facades\AliWeather::IpToWeather($data);
        return $res;
    }


    /**
     * 未来15天预报
     * @return array
     */
    public function DayFifteen(){
        $data = [
            'area' => '南安',//地区名称。
            'areaid' => '101230506',//地区id. 此参数和area必须二选一输入一个。
        ];
        $res = \App\Facades\AliWeather::DayFifteen($data);
        return $res;
    }

    /**
     * 地名查询id
     * @return array
     */
    public function AreaToId(){
        $data = [
            'area' => '银川',//地区名称。
        ];
        $res = \App\Facades\AliWeather::AreaToId($data);
        return $res;
    }

    /**
     * 历史天气查询
     * @return array
     */
    public function WeatherHistory(){
        $data = [
            'area' => '银川',//地区名称
            'month' => '202011',//查询的月份，格式yyyyMM。最早的数据是2015年1月。
        ];
        $res = \App\Facades\AliWeather::WeatherHistory($data);
        return $res;
    }
}
