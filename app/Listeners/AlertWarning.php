<?php


namespace App\Listeners;


use App\Models\Assets\Asset;
use App\Models\Assets\Device;
use App\Models\Warning\Warning;
use App\Models\Warning\WarningConfig;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertWarning
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     *
     * @param \App\Events\Alert $event
     * @return void
     */
    public function handle(\App\Events\Alert $event)
    {
        try {
            foreach ($event->data as $ks => $val){
                $asset_name = Device::where('id',$val['entity_id'])->first();
                $head = $asset_name['name'];
                $config = WarningConfig::getConfig($val['entity_id']);
                if(!empty($config)){
                    $warn_config = json_decode($config['config'],true);
                    $result = [];
                    $arr = [];
                    foreach ($warn_config as $key => $value){
                        if($val['key'] == $value['field']){
                            $result[] = $value;
                        }
                    }
                    foreach ($result as $k => $v){
                        if(empty($arr)){
                            $arr[] = "(".$val['dbl_v'] . $v['condition'] . $v['value'].")";
                        }else{
                            $arr[] = array_pop($arr) . $v['operator'] . "(".$val['dbl_v'] . $v['condition'] . $v['value'].")";
                        }
                    }
                    $conditions = array_pop($arr);
                    if (eval("return {$conditions};")) {
                        Warning::insertWarning('1',$head.$config['message'],$val['entity_id']);
                        //email notify
                        $this->SendEmail($val['entity_id'],$head.$config['message']);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Warning handle error', [$e->getCode(), $e->getMessage()]);
        }
    }

    function SendEmail($device_id,$msg){
        $deviceData = Device::where('id',$device_id)->first();
        $asset = Asset::where('id',$deviceData['asset_id'])->first();
        $user = User::where('business_id',$asset['business_id'])->get();
        foreach ($user as $key => $val){
//            if($val['email'] == '573595216@qq.com' || $val['email'] == '635885433@qq.com'){
                $email = $val['email'];
                Mail::raw($msg, function ($message) use($email){
                    $message ->to($email)->subject('自动化预警信息');
                });
//            }
        }
    }
}
