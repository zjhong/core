<?php

namespace App\Models\Assets;


use App\Models\Base;
use App\Models\Panels\Dashboard;
use Illuminate\Support\Facades\DB;

class Asset extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asset';

    protected $fillable = ['additional_info', 'customer_id', 'name', 'label', 'search_text', 'type', 'parent_id', 'tier'];

    /**
     * 获取下面的所有设备
     */
    public function devices()
    {
        return $this->hasMany('App\Models\Assets\Device');
    }

    public function getWidgetDataLevelOne($business_id){
        return DB::table($this->table)->where('business_id',$business_id)->where('parent_id',0)->select('id','name','business_id')->get()->toArray();
    }

    public function getWidgetDataLevelTwo($id){
        return DB::table($this->table)->where('parent_id',$id)->select('id','name','customer_id','business_id')->get()->toArray();
    }

    public function deleteData($id){
        return DB::table($this->table)->where('id',$id)->delete();
    }

    public function getDataById($id){
        return DB::table($this->table)->where('business_id',$id)->get()->first();
    }

    public function updateData($id,$data){
        return DB::table($this->table)->where('id',$id)->update($data);
    }
}
