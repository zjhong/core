<?php
return [
    //插件类型
    'type' => 'app',
    //插件名
    'name' => '气象站插件',
    //插件描述
    'description' => '气象站插件 Weather station plug-in',
    //版本号
    'version' => '1.0.0',
    //作者
    'author' => '',
    //邮箱
    'email' => '',
    //图表组件
    'widgets' => [
        //唯一标识: WeatherStations:air_quality
        'air_quality' => [
            //图表名
            'name' => '空气质量',
            //图表描述
            'description' => '空气质量',
            //处理类
            'class' => \App\Extensions\WeatherStations\Actions\AirQuality::class,
            //缩略图
            'thumbnail' => '/temperature.png',
            'template' => 'air_quality',
            //数据字段名
            'fields' => [
                "pm" => [
                    'name' => 'PM2.5',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mg/m3'
                ],
                "co2" => [
                    'name' => '二氧化碳',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mg/h'
                ],
                "o3" => [
                    'name' => '臭氧浓度',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mg/h'
                ],
                "so2" => [
                    'name' => '二氧化硫',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mg/h'
                ],
                "nai" => [
                    'name' => '负氧离子',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mg/h'
                ],
                "co" => [
                    'name' => '一氧化碳',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mg/h'
                ],
            ],
        ],
        //唯一标识: WeatherStations:environmental_parameters
        'environmental_parameters' => [
            //图表名
            'name' => '环境参数',
            //图表描述
            'description' => '环境参数',
            //处理类
            'class' => \App\Extensions\WeatherStations\Actions\EnvironmentalParameters::class,
            //缩略图
            'thumbnail' => '/temperature.png',
            'template' => 'environmental_parameters',
            //数据字段名
            'fields' => [
                "temp" => [
                    'name' => '温度',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => '℃'
                ],
                "hum" => [
                    'name' => '湿度',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => '%'
                ],
                "yl" => [
                    'name' => '气压',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'kpa'
                ],
                "rainfall" => [
                    'name' => '降雨概率',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => '%'
                ],
                "wind" => [
                    'name' => '风向',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'm/s'
                ],
                "uv" => [
                    'name' => '紫外线',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_SCROLL,
                    'symbol' => 'mW/m3'
                ],
            ],
        ],
        //唯一标识: WeatherStations:weather_day
        'weather_day' => [
            //图表名
            'name' => '24小时天气概况',
            //图表描述
            'description' => '24小时天气概况',
            //处理类
            'class' => \App\Extensions\WeatherStations\Actions\WeatherDay::class,
            //缩略图
            'thumbnail' => '/temperature.png',
            'template' => 'weather_day',
            //数据字段名
            'fields' => [
                "temps" => [
                    'name' => '温度',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_CHART,
                    'symbol' => '℃'
                ],
            ],
        ],
        //唯一标识: WeatherStations:weather_week
        'weather_week' => [
            //图表名
            'name' => ' 最近7天天气概况',
            //图表描述
            'description' => ' 最近7天天气概况',
            //处理类
            'class' => \App\Extensions\WeatherStations\Actions\WeatherWeek::class,
            //缩略图
            'thumbnail' => '/temperature.png',
            'template' => 'weather_week',
            //数据字段名
            'fields' => [
                "weather" => [
                    'name' => '天气',
                    'type' => \App\Extensions\BaseClass::FIELD_TYPE_CHART,
                ],
            ],
        ],
    ]
];
