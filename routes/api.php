<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('auth')->middleware('api')->namespace('Api')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register', 'AuthController@register');
});


// api routes
Route::middleware('auth:api')->namespace('Api')->group(function () {
    //Home
    Route::prefix('home')->namespace('Home')->group(function () {
        Route::post('status', 'HomeController@status'); //http://domain/api/home/status
        Route::post('events', 'HomeController@events'); //http://domain/api/home/events
        Route::post('list', 'HomeController@list'); //http://domain/api/home/list
        Route::post('chart', 'HomeController@chart'); //http://domain/api/home/chart
    });

    //Assets
    Route::prefix('asset')->namespace('Assets')->group(function () {
        Route::post('index', 'AssetController@index'); //http://domain/api/asset/index
        Route::post('add', 'AssetController@add'); //http://domain/api/asset/add
        Route::post('list', 'AssetController@list'); //http://domain/api/asset/list
        Route::post('edit', 'AssetController@edit'); //http://domain/api/asset/edit
        Route::post('delete', 'AssetController@delete'); //http://domain/api/asset/delete
        Route::post('widget', 'AssetController@widget'); //http://domain/api/asset/widget
        //TODO
        Route::post('work_index', 'BusinessController@index'); //http://domain/api/asset/work_index
        Route::post('work_add', 'BusinessController@add'); //http://domain/api/asset/work_add
        Route::post('work_edit', 'BusinessController@edit'); //http://domain/api/asset/work_edit
        Route::post('work_delete', 'BusinessController@delete'); //http://domain/api/asset/work_delete
        //TODO
    });

    //Devices
    Route::prefix('device')->namespace('Devices')->group(function () {
        Route::post('index', 'DeviceController@index'); //http://domain/api/device/index
        Route::post('edit', 'DeviceController@edit'); //http://domain/api/device/edit
        //TODO
    });

    //Customers
    Route::prefix('customer')->namespace('Customers')->group(function () {
        Route::post('index', 'CustomerController@index'); //http://domain/api/customer/index
        Route::post('add', 'CustomerController@add'); //http://domain/api/customer/add
        Route::post('edit', 'CustomerController@edit'); //http://domain/api/customer/edit
        Route::post('delete', 'CustomerController@delete'); //http://domain/api/customer/delete
        //TODO
    });

    //Users
    Route::prefix('user')->namespace('Users')->group(function () {
        Route::post('index', 'UserController@index'); //http://domain/api/user/index
        Route::post('add', 'UserController@add'); //http://domain/api/user/add
        Route::post('edit', 'UserController@edit'); //http://domain/api/user/edit
        Route::post('update', 'UserController@update'); //http://domain/api/user/update
        Route::post('delete', 'UserController@delete'); //http://domain/api/user/delete
        Route::post('permission', 'UserController@permission'); //http://domain/api/user/permission
        //TODO
    });

    //Dashboard
    Route::prefix('dashboard')->namespace('Dashboards')->group(function () {
        Route::post('index', 'DashboardController@index'); //http://domain/api/dashboard/index
        Route::post('add', 'DashboardController@add'); //http://domain/api/dashboard/add
        Route::post('paneladd', 'DashboardController@panelAdd'); //http://domain/api/dashboard/paneladd
        Route::post('paneledit', 'DashboardController@panelEdit'); //http://domain/api/dashboard/paneledit
        Route::post('paneldelete', 'DashboardController@panelDelete'); //http://domain/api/dashboard/paneldelete
        Route::post('list', 'DashboardController@list'); //http://domain/api/dashboard/list
        Route::post('edit', 'DashboardController@edit'); //http://domain/api/dashboard/edit
        Route::post('delete', 'DashboardController@delete'); //http://domain/api/dashboard/delete
        Route::post('business', 'DashboardController@business'); //http://domain/api/dashboard/business
        Route::post('property', 'DashboardController@property'); //http://domain/api/dashboard/property
        Route::post('device', 'DashboardController@device'); //http://domain/api/dashboard/device
        Route::post('inserttime', 'DashboardController@insertTime'); //http://domain/api/dashboard/insertTime
        Route::post('gettime', 'DashboardController@getTime'); //http://domain/api/dashboard/getTime
        Route::post('dashboard', 'DashboardController@dashboard'); //http://domain/api/dashboard/dashboard
        Route::post('updateDashboard', 'DashboardController@updateDashboard'); //http://domain/api/dashboard/updateDashboard
        Route::post('realTime', 'DashboardController@realTime'); //http://domain/api/dashboard/realTime
        Route::post('component', 'DashboardController@component'); //http://domain/api/dashboard/component
        //TODO
    });

    //Markets TODO
    Route::prefix('markets')->namespace('Markets')->group(function () {
        Route::post('index', 'MarketsController@index'); //http://domain/api/markets/index
        Route::post('add', 'MarketsController@add'); //http://domain/api/markets/add
        Route::post('edit', 'MarketsController@edit'); //http://domain/api/markets/edit
        Route::post('delete', 'MarketsController@delete'); //http://domain/api/markets/delete
        Route::post('list', 'MarketsController@list'); //http://domain/api/markets/list
        //TODO
    });

    //Home TODO
    Route::prefix('index')->namespace('Home')->group(function () {
        Route::post('device', 'HomeController@index'); //http://domain/api/Home/index
        Route::post('show', 'HomeController@show'); //http://domain/api/Home/show
        //TODO
    });

    //Warning TODO
    Route::prefix('warning')->namespace('Warning')->group(function () {
        Route::post('index', 'WarningController@index'); //http://domain/api/Warning/index
        Route::post('list', 'WarningController@list'); //http://domain/api/Warning/list
        Route::post('field', 'WarningConfigController@field'); //http://domain/api/Warning/field
        Route::post('show', 'WarningConfigController@show'); //http://domain/api/Warning/show
        Route::post('add', 'WarningConfigController@add'); //http://domain/api/Warning/add
        Route::post('update', 'WarningConfigController@update'); //http://domain/api/Warning/update
        Route::post('edit', 'WarningConfigController@edit'); //http://domain/api/Warning/edit
        Route::post('delete', 'WarningConfigController@delete'); //http://domain/api/Warning/delete
        //TODO
    });

    //Operation TODO
    Route::prefix('operation')->namespace('Operation')->group(function () {
        Route::post('index', 'OperationLogController@index'); //http://domain/api/Operation/index
        Route::post('list', 'OperationLogController@list'); //http://domain/api/Operation/list
        //TODO
    });

    //Amap TODO
    Route::prefix('amap')->namespace('Amap')->group(function () {
        Route::get('index', 'AmapController@weatherInfo'); //http://domain/api/Amap/index
        Route::post('show', 'AmapController@show'); //http://domain/api/Amap/show
        Route::post('now', 'AmapController@now'); //http://domain/api/Amap/now
        Route::post('weather', 'AmapController@weather'); //http://domain/api/Amap/weather
        Route::post('environment', 'AmapController@environment'); //http://domain/api/Amap/environment
        //TODO
    });

    //Automation TODO
    Route::prefix('automation')->namespace('Automation')->group(function () {
        Route::post('index', 'AutomationController@index'); //http://domain/api/Automation/index
        Route::post('show', 'AutomationController@show'); //http://domain/api/Automation/show
        Route::post('add', 'AutomationController@add'); //http://domain/api/Automation/add
        Route::post('status', 'AutomationController@status'); //http://domain/api/Automation/status
        Route::post('symbol', 'AutomationController@symbol'); //http://domain/api/Automation/symbol
        Route::post('edit', 'AutomationController@edit'); //http://domain/api/Automation/edit
        Route::post('update', 'AutomationController@update'); //http://domain/api/Automation/update
        Route::post('delete', 'AutomationController@delete'); //http://domain/api/Automation/delete
        Route::post('property', 'AutomationController@property'); //http://domain/api/Automation/property
        Route::post('instruct', 'AutomationController@instruct'); //http://domain/api/Automation/instruct
        //TODO
    });

    //AliWeather TODO
    Route::prefix('aliweather')->namespace('AliWeather')->group(function () {
        Route::post('areatoweather', 'AliWeatherController@AreaToWeather'); //http://domain/api/aliweather/areatoweather
        Route::post('gpstoweather', 'AliWeatherController@GpsToWeather'); //http://domain/api/aliweather/gpstoweather
        Route::post('spottoweather', 'AliWeatherController@SpotToWeather'); //http://domain/api/aliweather/spottoweather
        Route::post('phonepostcodeweather', 'AliWeatherController@PhonePostCodeWeather'); //http://domain/api/aliweather/phonepostcodeweather
        Route::post('iptoweather', 'AliWeatherController@IpToWeather'); //http://domain/api/aliweather/iptoweather
        Route::post('dayfifteen', 'AliWeatherController@DayFifteen'); //http://domain/api/aliweather/dayfifteen
        Route::post('areatoId', 'AliWeatherController@AreaToId'); //http://domain/api/aliweather/areatoId
        Route::post('weatherhistory', 'AliWeatherController@WeatherHistory'); //http://domain/api/aliweather/weatherhistory
        //TODO
    });

    //Structure TODO
    Route::prefix('structure')->namespace('Assets')->group(function () {
        Route::post('field', 'DataStructureController@field'); //http://domain/api/structure/field
        Route::post('add', 'DataStructureController@add'); //http://domain/api/structure/add
        Route::post('list', 'DataStructureController@list'); //http://domain/api/structure/list
        Route::post('update', 'DataStructureController@update'); //http://domain/api/structure/update
        Route::post('delete', 'DataStructureController@delete'); //http://domain/api/structure/delete
        //TODO
    });

    //Navigation TODO
    Route::prefix('navigation')->namespace('Navigation')->group(function () {
        Route::post('list', 'NavigationController@list'); //http://domain/api/navigation/list
        Route::post('add', 'NavigationController@add'); //http://domain/api/navigation/add
        //TODO
    });

    //Kv TODO
    Route::prefix('kv')->namespace('Kv')->group(function () {
        Route::post('index', 'KvController@index'); //http://domain/api/kv/index
        Route::post('list', 'KvController@list'); //http://domain/api/kv/list
        Route::post('export', 'KvController@export'); //http://domain/api/kv/export
        //TODO
    });

    //System TODO
    Route::prefix('system')->namespace('System')->group(function () {
        Route::post('index', 'SystemController@index'); //http://domain/api/system/index
        Route::post('list', 'SystemController@list'); //http://domain/api/system/list
        //TODO
    });

    //Production TODO
    Route::prefix('production')->namespace('Production')->group(function () {
        Route::post('index', 'ProductionController@index'); //http://domain/api/production/index
        Route::post('add', 'ProductionController@add'); //http://domain/api/production/add
        Route::post('edit', 'ProductionController@edit'); //http://domain/api/production/edit
        Route::post('update', 'ProductionController@update'); //http://domain/api/production/update
        Route::post('delete', 'ProductionController@delete'); //http://domain/api/production/delete
        //TODO
    });

    //Dictionary TODO
    Route::prefix('dictionary')->namespace('Dictionary')->group(function () {
        Route::post('index', 'DictionaryController@index'); //http://domain/api/dictionary/index
        //TODO
    });
});
