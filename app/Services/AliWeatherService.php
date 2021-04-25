<?php


namespace App\Services;

/**
 *
 * 调用demo App\Http\Controllers\Api\AliWeather\AliWeatherController
 */
class AliWeatherService
{
    const ThePrefix = 'https://ali-weather.showapi.com/';
    const Method = "GET";
    const ipInfp = 'https://restapi.amap.com/v3/geocode/regeo';  //经纬度获取城市地址

    /**
     * 地名查询天气
     * @param $data
     * @return array
     */
    public function AreaToWeather(array $data)
    {
        $result = $this->HttpGet('area-to-weather',$data);
        return $result;
    }

    /**
     * 经纬度查询天气
     * @param $data
     * @return array
     */
    public function GpsToWeather(array $data){
        $result = $this->HttpGet('gps-to-weather',$data);
        return $result;
    }

    /**
     * 景点名称查询天气
     * @param $data
     * @return array
     */
    public function SpotToWeather(array $data){
        $result = $this->HttpGet('spot-to-weather',$data);
        return $result;
    }

    /**
     * 区号邮编查询天气
     * @param $data
     * @return array
     */
    public function PhonePostCodeWeather(array $data){
        $result = $this->HttpGet('phone-post-code-weeather',$data);
        return $result;
    }

    /**
     * IP查询天气
     * @param $data
     * @return array
     */
    public function IpToWeather(array $data){
        $result = $this->HttpGet('ip-to-weather',$data);
        return $result;
    }

    /**
     * 未来15天预报
     * @param $data
     * @return array
     */
    public function DayFifteen(array $data){
        $result = $this->HttpGet('day15',$data);
        return $result;
    }

    /**
     * 地名查询id
     * @param $data
     * @return array
     */
    public function AreaToId(array $data){
        $result = $this->HttpGet('area-to-id',$data);
        return $result;
    }

    /**
     * 历史天气查询
     * @param $data
     * @return array
     */
    public function WeatherHistory(array $data){
        $result = $this->HttpGet('weatherhistory',$data);
        return $result;
    }

    /**
     * 查询24小时预报
     * @param $data
     * @return array
     */
    public function hour24(array $data){
        $result = $this->HttpGet('hour24',$data);
        return $result;
    }

    public function getCityName(array $data){
        $lgtData = json_decode($this->Http(self::ipInfp,['parameters' => '1', 'key' => env('AMAP_KEY', ''), 'location'  => $data['logt']]),true);
        $lgtData['regeocode']['addressComponent']['city'] = empty($lgtData['regeocode']['addressComponent']['city']) ? $lgtData['regeocode']['addressComponent']['province'] : $lgtData['regeocode']['addressComponent']['city'];
        $data['area'] = $lgtData['regeocode']['addressComponent']['city'];
        return $data;
    }

    /**
     * @description Make  call
     * @param       $path
     * @param array $params
     * @return array
     */
    public function HttpGet($path, array $params){
        $host = self::ThePrefix;
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . env('ALIWEATHER_KEY', ''));
        $url = $host . $path . "?" . http_build_query($params);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::Method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response,true);
    }


    /**
     * @description Make  call
     * @param       $url
     * @param array $params
     */
    public static function Http($url, array $params)
    {
        $query = http_build_query($params);
        $ch = curl_init($url . '?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
