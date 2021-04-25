<?php

namespace App\Providers;

use App\Events\Alert;
use App\Events\Telemetry;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Telemetry::class => [
            'App\Listeners\DataConverter', //数据解析
            'App\Listeners\CoordinateTransform', //GPS坐标处理
            'App\Listeners\Telemetry', //默认数据处理
        ],
        Alert::class => [
            'App\Listeners\AlertWarning', //处理预警
        ],
        'App\Events\Strategy' => [
            'App\Listeners\HandlingStrategy',//处理策略
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
