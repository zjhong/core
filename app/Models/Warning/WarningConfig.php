<?php


namespace App\Models\Warning;


use App\Models\Base;

class WarningConfig extends Base
{
    /**
     * The table warning_config with the model.
     *
     * @var string
     */
    protected $table = 'warning_config';

    protected $fillable = ['wid','bid', 'name','describe','status','config','message','sensor'];

    public static function getConfig($bid){
        return WarningConfig::where('bid',$bid)->first();
    }
}
