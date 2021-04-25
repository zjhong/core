<?php

namespace App\Models\Panels;

use App\Models\Base;

class Widget extends Base
{

    protected $table = 'widget';
    protected $fillable = ['dashboard_id', 'config', 'type', 'action','updated_at','device_id','widget_identifier','asset_id'];
    /**
     * @var array
     */
    protected $casts = [
        'config' => 'array', //自动json
    ];

}
