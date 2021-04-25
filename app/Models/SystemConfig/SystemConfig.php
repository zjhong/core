<?php


namespace App\Models\SystemConfig;


use App\Models\Base;

class SystemConfig extends Base
{
    protected $table = 'system_config';
    protected $fillable = ['type', 'config'];
}
