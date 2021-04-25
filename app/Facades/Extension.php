<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Extension
 * @method static init()
 * @method static handle(array $data, bool $initial = false)
 * @method static getExtensionInfo($widgetName = '')
 * @method static getWidgetFields($widgetIdentifier)
 * @method static getTemplateName($extensionName, $templateName)
 * @package App\Facades
 */
class Extension extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '\App\Services\ExtensionService';
    }
}
