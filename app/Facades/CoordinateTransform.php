<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Extension
 * @method static bd09togcj02($lng, $lat)
 * @method static gcj02tobd09($lng, $lat)
 * @method static wgs84togcj02($lng, $lat)
 * @method static gcj02towgs84($lng, $lat)
 * @method static wgs84tobd09($lng, $lat)
 * @method static bd09towgs84($lng, $lat)
 * @package App\Facades
 */
class CoordinateTransform extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '\App\Services\CoordinateTransformService';
    }
}
