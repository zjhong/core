<?php


namespace App\Http\Controllers\Api\Amap;


use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Log;

class AmapController extends Controller
{
    const ipInfp = 'https://restapi.amap.com/v3/geocode/regeo';  //经纬度获取城市地址
    const weatherInfo = 'https://restapi.amap.com/v3/weather/weatherInfo';  //获取城市天气信息

    public function weatherInfo(){
        try{
            $post     = request()->all();
            $ipData = json_decode($this->HTTPGet(self::ipInfp,['parameters' => '1', 'key' => env('AMAP_KEY', ''), 'location'  => $post['logt'], 'output' => 'JSON']),true);
            $ipData['regeocode']['addressComponent']['city'] = empty($ipData['regeocode']['addressComponent']['city']) ? $ipData['regeocode']['addressComponent']['province'] : $ipData['regeocode']['addressComponent']['city'];
            $Data =json_decode($this->HTTPGet(self::weatherInfo,['parameters' => '1', 'key' => env('AMAP_KEY', ''), 'extensions'  => 'all', 'city'  => $ipData['regeocode']['addressComponent']['city'], 'output' => 'JSON']),true);
            $RealData =json_decode($this->HTTPGet(self::weatherInfo,['parameters' => '1', 'key' => env('AMAP_KEY', ''), 'extensions'  => 'base', 'city'  => $ipData['regeocode']['addressComponent']['city'], 'output' => 'JSON']),true);
            if(isset($RealData['lives'])){
                $RealData['lives'][0]['reporttime'] = date('H:i:s',strtotime($RealData['lives']['0']['reporttime']));
                $RealData['lives'][0]['gz'] = rand(100,200);
                $RealData['lives'][0]['cot'] = rand(220,500);
                $RealData['lives'][0]['pm'] = '优';
                $Data['real'] = $RealData['lives']['0'];
            }
            return $Data;
        } catch (\Exception $e) {
            return $this->jsonResponse(500, 'lat.lnt error',[$e->getMessage()]);
        }
    }

    /*
     * 查询气象站
     * */
    public function now(){
        $post     = request()->all();
//        $Arr['sunrise'] = date_sunset(time(),SUNFUNCS_RET_STRING,38.46667,106.26667);//日落
//        $Arr['sunset'] = date_sunrise(time(),SUNFUNCS_RET_STRING,38.46667,106.26667);//日出
//        $data = [
//            'from' => '5',//输入的坐标类型
//            'lat' => '38.46667',//纬度值
//            'lng' => '106.26667',//经度值
//        ];
//        $res = \App\Facades\AliWeather::GpsToWeather($data);
//        $Arr['now'] = $res['showapi_res_body']['now'];
        $Arr = '{"sunrise":"17:34","sunset":"07:53","now":{"weather_code":"02","aqiDetail":{"num":"304","co":"1.9","so2":"17","area":"\u94f6\u5ddd","o3":"9","no2":"49","aqi":"108","quality":"\u8f7b\u5ea6\u6c61\u67d3","pm10":"121","pm2_5":"81","o3_8h":"10","primary_pollutant":"\u9897\u7c92\u7269(PM2.5)"},"wind_direction":"\u897f\u5317\u98ce","temperature_time":"11:00","wind_power":"2\u7ea7","aqi":"108","sd":"78%","weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/02.png","weather":"\u9634","temperature":"-2"}}';
        $Arr = json_decode($Arr,true);
        $Arr['now']['aqiDetail']['nai'] = '0';
        $Arr['now']['aqiDetail']['co2'] = '0';
        return $this->jsonResponse(200, 'success', $Arr);
    }

    /*
     * 查询未来七天时间
     * */
    public function show(){
        $post     = request()->all();
//        $Arr = [];
//        $data = [
//            'area' => '银川',//地区名称。
//        ];
//        $res = \App\Facades\AliWeather::DayFifteen($data);
//        $Arr['future'] = array_slice($res['showapi_res_body']['dayList'],0,7);
        $Arr = '{"future":[{"night_weather_code":"14","day_weather":"\u5c0f\u96ea","night_weather":"\u5c0f\u96ea","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"14","daytime":"20201201","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/14.png","night_air_temperature":"-6","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-1","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/14.png"},{"night_weather_code":"01","day_weather":"\u6674","night_weather":"\u591a\u4e91","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"00","daytime":"20201202","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/00.png","night_air_temperature":"-8","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-3","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/01.png"},{"night_weather_code":"01","day_weather":"\u591a\u4e91","night_weather":"\u591a\u4e91","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"01","daytime":"20201203","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/01.png","night_air_temperature":"-9","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-4","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/01.png"},{"night_weather_code":"00","day_weather":"\u591a\u4e91","night_weather":"\u6674","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"01","daytime":"20201204","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/01.png","night_air_temperature":"-7","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-5","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/00.png"},{"night_weather_code":"01","day_weather":"\u6674","night_weather":"\u591a\u4e91","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"00","daytime":"20201205","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/00.png","night_air_temperature":"-6","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-1","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/01.png"},{"night_weather_code":"00","day_weather":"\u591a\u4e91","night_weather":"\u6674","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"01","daytime":"20201206","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/01.png","night_air_temperature":"-6","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-1","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/00.png"},{"night_weather_code":"00","day_weather":"\u6674","night_weather":"\u6674","areaCode":"640100","night_wind_power":"0-3\u7ea7","areaid":"101170101","day_wind_power":"0-3\u7ea7","day_weather_code":"00","daytime":"20201207","area":"\u94f6\u5ddd","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/00.png","night_air_temperature":"-10","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day_air_temperature":"-1","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/00.png"}]}';
        $Arr = json_decode($Arr,true);
        foreach ($Arr['future'] as $key => $val){
            $Arr['future'][$key]['daytime'] = $this->applyDay(date('w',strtotime($val['daytime'])));
        }
        return $this->jsonResponse(200, 'success', $Arr);
    }

    /*
     * 24小时天气状态
     * */
    public function weather(){
        $post     = request()->all();
//        $data = [
//            'area' => '银川',//地区名称
//        ];
//        $res = \App\Facades\AliWeather::hour24($data);
        $res = '{"showapi_res_error":"","showapi_res_id":"5fc5b3978d57ba0347589a2b","showapi_res_code":0,"showapi_res_body":{"ret_code":0,"area":"\u94f6\u5ddd","areaCode":"640100","showapi_fee_code":-1,"areaid":"101170101","hourList":[{"weather_code":"14","time":"202012011100","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-4"},{"weather_code":"06","time":"202012011200","area":"\u94f6\u5ddd","wind_direction":"\u5317\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u96e8\u5939\u96ea","temperature":"-3"},{"weather_code":"06","time":"202012011300","area":"\u94f6\u5ddd","wind_direction":"\u5317\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u96e8\u5939\u96ea","temperature":"-2"},{"weather_code":"06","time":"202012011400","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u96e8\u5939\u96ea","temperature":"-1"},{"weather_code":"14","time":"202012011500","area":"\u94f6\u5ddd","wind_direction":"\u4e1c\u5317\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-2"},{"weather_code":"14","time":"202012011600","area":"\u94f6\u5ddd","wind_direction":"\u4e1c\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-2"},{"weather_code":"14","time":"202012011700","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-2"},{"weather_code":"01","time":"202012011800","area":"\u94f6\u5ddd","wind_direction":"\u5357\u98ce","wind_power":"3-4\u7ea7 \u5fae\u98ce  5.5~7.9m\/s","areaid":"101170101","weather":"\u591a\u4e91","temperature":"-3"},{"weather_code":"14","time":"202012011900","area":"\u94f6\u5ddd","wind_direction":"\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"14","time":"202012012000","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"14","time":"202012012100","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"14","time":"202012012200","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"01","time":"202012012300","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u591a\u4e91","temperature":"-3"},{"weather_code":"14","time":"202012020000","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"14","time":"202012020100","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"14","time":"202012020200","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-3"},{"weather_code":"14","time":"202012020300","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-4"},{"weather_code":"14","time":"202012020400","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-4"},{"weather_code":"14","time":"202012020500","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-4"},{"weather_code":"14","time":"202012020600","area":"\u94f6\u5ddd","wind_direction":"\u897f\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-5"},{"weather_code":"14","time":"202012020700","area":"\u94f6\u5ddd","wind_direction":"\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-5"},{"weather_code":"14","time":"202012020800","area":"\u94f6\u5ddd","wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u5c0f\u96ea","temperature":"-5"},{"weather_code":"01","time":"202012020900","area":"\u94f6\u5ddd","wind_direction":"\u5357\u98ce","wind_power":"3-4\u7ea7 \u5fae\u98ce  5.5~7.9m\/s","areaid":"101170101","weather":"\u591a\u4e91","temperature":"-5"},{"weather_code":"01","time":"202012021000","area":"\u94f6\u5ddd","wind_direction":"\u5357\u98ce","wind_power":"0-3\u7ea7 \u5fae\u98ce  <5.4m\/s","areaid":"101170101","weather":"\u591a\u4e91","temperature":"-5"}]}}';
        $res = json_decode($res,true);
        foreach ($res['showapi_res_body']['hourList'] as $key => $val){
            $res['showapi_res_body']['hourList'][$key]['temperature'] = rand(-5,10);
            $res['showapi_res_body']['hourList'][$key]['time'] = date('H:i',strtotime($val['time']));
        }
        return $this->jsonResponse(200, 'success', $res['showapi_res_body']['hourList']);
    }

    /*
     * 环境参数
     * */
    public function environment(){
        $post     = request()->all();
//        $data = [
//            'area' => '银川市',//地区名称
//            'needAlarm' => '1',//天气预
//            //
//            //警1为返回，0为不返回。
//            'needIndex' => '0',//指数数据1为返回，0为不返回。
//            'needMoreDay' => '0',//是否需要返回7天数据中的后4天。1为返回，0为不返回。
//        ];
//        $res = \App\Facades\AliWeather::AreaToWeather($data);
//        $arr['temperature'] = $res['showapi_res_body']['now']['temperature'];
//        $arr['sd'] = $res['showapi_res_body']['now']['sd'];
//        $arr['weather'] = $res['showapi_res_body']['now']['weather'];
//        $arr['weather_pic'] = $res['showapi_res_body']['now']['weather_pic'];
//        $arr['wind_direction'] = $res['showapi_res_body']['now']['wind_direction'];
//        $arr['wind_power'] = $res['showapi_res_body']['now']['wind_power'];
//        $arr['f1'] = $res['showapi_res_body']['f1'];
        $arr = '{"temperature":"-2","sd":"77%","weather":"\u591a\u4e91","weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/01.png","wind_direction":"\u897f\u5317\u98ce","wind_power":"2\u7ea7","f1":{"night_weather_code":"14","day_weather":"\u5c0f\u96ea","night_weather":"\u5c0f\u96ea","jiangshui":"72%","air_press":"880.5hPa","night_wind_power":"0-3\u7ea7 <5.4m\/s","day_wind_power":"0-3\u7ea7 <5.4m\/s","day_weather_code":"14","sun_begin_end":"07:52|17:34","ziwaixian":"\u6700\u5f31","day_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/day\/14.png","weekday":2,"night_air_temperature":"-6","day_air_temperature":"-1","day_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411","day":"20201201","night_weather_pic":"http:\/\/app1.showapi.com\/weather\/icon\/night\/14.png","night_wind_direction":"\u65e0\u6301\u7eed\u98ce\u5411"}}';
        $arr = json_decode($arr,true);
        return $this->jsonResponse(200, 'success', $arr);
    }

    function applyDay($da){
        if( $da == "1" ){
            $str = "星期一";
        }else if( $da == "2" ){
             $str = "星期二";
        }else if( $da == "3" ){
             $str = "星期三";
        }else if( $da == "4" ){
             $str = "星期四";
        }else if( $da == "5" ){
             $str = "星期五";
        }else if( $da == "6" ){
             $str = "星期六";
        }else if( $da == "0" ){
             $str = "星期日";
        }else{
             $str = "数据有误!";
        }
        return $str;
    }
}
