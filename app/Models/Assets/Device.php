<?php


namespace App\Models\Assets;


use App\Models\Base;
use Illuminate\Support\Facades\DB;

class Device extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'device';

    protected $fillable = ['asset_id', 'token','additional_info','customer_id','type','name','label','search_text','extension'];

    public function deleteData($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }

    public static function getTokenByDeviceId($device_id)
    {
        return self::where('id', $device_id)->value('token');
    }

    public function getDeviceDataByID($device_id){
        return DB::table($this->table)->where('asset_id',$device_id)->select('id','asset_id','type','name')->get()->toArray();
    }

    public function updateDeviceData($id,$data){
        return DB::table($this->table)->where('id',$id)->update($data);
    }
}
