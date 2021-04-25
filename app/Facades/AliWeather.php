<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Extension
 * @method static AreaToWeather(array $data)
 * @method static GpsToWeather(array $data)
 * @method static SpotToWeather(array $data)
 * @method static PhonePostCodeWeather(array $data)
 * @method static IpToWeather(array $data)
 * @method static DayFifteen(array $data)
 * @method static AreaToId(array $data)
 * @method static WeatherHistory(array $data)
 * @method static hour24(array $data)
 * @method static getCityName(array $data)
 * @package App\Facades
 */
class AliWeather  extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '\App\Services\AliWeatherService';
    }
}
