<?php


namespace App\Models\Automation;


use App\Facades\Telemetry;
use App\Models\Assets\Asset;
use App\Models\Assets\Device;
use App\Models\Base;
use App\Models\Telemetry\Kv;
use Illuminate\Support\Facades\Log;

class Conditions extends Base
{
    protected $table = 'conditions';

    protected $fillable = ['business_id', 'name','describe','status','config','sort','type','issued'];

    public $StatusArr = [
        '0' => '每天执行',
        '1' => '每一分钟执行一次',
        '2' => '每五分钟执行一次',
        '3' => '每十分钟执行一次',
        '4' => '每一小时执行一次',
        '5' => '每三小时执行一次',
        '6' => '每六小时执行一次',
        '7' => '每十二小时执行一次',
    ];

    public $symbol = [
        '0' => '<',
        '1' => '>',
        '2' => '==',
        '3' => '<=',
        '4' => '>=',
    ];

    public $symbolArr = [
        [
            'id' => '<',
            'name' => '小于',
        ],[
            'id' => '>',
            'name' => '大于',
        ],[
            'id' => '==',
            'name' => '等于',
        ],[
            'id' => '<=',
            'name' => '小于等于',
        ],[
            'id' => '>=',
            'name' => '大于等于',
        ]
    ];

    public static function disposeField($res,$keyed,$conditions){
        $result = [];
        foreach ($res as $k => $v){
            foreach ($v['widgets'] as $key => $value){
                if($key == $keyed){
                    foreach ($value['fields'] as $field_key => $field){
                        if($conditions == 'condition'){
                            $result[$field_key]['key'] = $field_key;
                            $result[$field_key]['name'] = is_array($field)?$field['name']:$field;
                            $result[$field_key]['type'] = is_array($field)?$field['type']:1;
                        }else{
                            if(isset($field['type']) && $field['type'] != 1){
                                $result[$field_key]['key'] = $field_key;
                                $result[$field_key]['name'] = is_array($field)?$field['name']:$field;
                                $result[$field_key]['type'] = is_array($field)?$field['type']:1;
                            }
                        }
                    }
                }
            }
        }
        $result = array_values($result);
        return $result;
    }

    public static function disposeComponent($read_static_cache,$asset_id){
        $result = [];
        $device = Device::where('asset_id',$asset_id)->select('id','type')->get();
        $device = json_decode(json_encode($device,JSON_UNESCAPED_UNICODE),true);
        foreach ($device as $type) {
            foreach ($read_static_cache as $key => $val) {
                $arr = [];
                if ($key == $type['type']) {
                    foreach ($val['widgets'] as $k => $v) {
                        $arr[$k]['name'] = $v['name'];
                        $arr[$k]['key'] = $key . ':' . $k;
                    }
                    $result[$key]['name'] = $val['name'];
                    $result[$key]['id'] = $type['id'];
                    $result[$key]['widgets'] = array_values($arr);
                }
            }
        }
        $result = array_values($result);
        return $result;
    }

    public static function disposeProperty($wid){
        $asset = new Asset();
        $assets = $asset::where('tier', 1)->where('business_id', $wid)->get();
        foreach ($assets as $key => &$val) {
            $two_assets = $asset::where('parent_id', $val['id'])->get();
            if (!$two_assets->isEmpty()) {
                $val['children'] = $two_assets;
                foreach ($val['children'] as $k => &$v) {
                    $there_assets = $asset::where('parent_id', $v['id'])->get();
                    if (!$there_assets->isEmpty()) {
                        $v['children'] = $there_assets;
                    }
                }
            }
        }
        return $assets;
    }

    public function ApplyResult($applyArr){
        foreach ($applyArr as $ArrVal) {
            $token = Device::getTokenByDeviceId($ArrVal['device_id']);
            $field = Kv::getLatestValue($ArrVal['device_id'], $ArrVal['field'], 0);
            if($field != $ArrVal['value']) {
                //查询协议类型
                $protocol = Device::where('id', $ArrVal['device_id'])->value('type');
                if($protocol != 'intelligentscreen'){
                    Telemetry::sendDataToClient($token, [
                        $ArrVal['field'] => intval($ArrVal['value'])
                    ]);
                }else{
                    Telemetry::sendDataToTCPClient($token, [
                        $ArrVal['field'] => intval($ArrVal['value'])
                    ]);
                }
            }
            Log::info(date('Y-m-d H:i:s') . 'conditions:strategy end', ['token' => $token, $ArrVal['field'] => $ArrVal['value']]);
        }
        return true;
    }
}
