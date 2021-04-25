<?php


namespace App\Listeners;


use App\Facades\Telemetry;
use App\Models\Assets\Asset;
use App\Models\Automation\Conditions;
use App\Models\Assets\Device;
use App\Models\Telemetry\Kv;
use Illuminate\Support\Facades\Log;

class HandlingStrategy
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
     * Handle the strategy.
     *
     * @param \App\Events\Strategy $strategy
     * @return void
     */
    public function handle(\App\Events\Strategy $strategy)
    {
        try {
            $condition = new Conditions();
            $device = Device::where('id', $strategy->data[0]['entity_id'])->first();
            $asset = Asset::where('id', $device['asset_id'])->first();
            //查询策略
            $conditionsData = Conditions::OrderBy('sort', 'asc')->where('status',1)->where('type', 1)->where('business_id', $asset['business_id'])->get();
            $applyArr = [];
            $fieldArr = [];
            foreach ($conditionsData as $key => $value) {
                $arr = [];
                $config = json_decode($value['config'], true);
                if (empty($config['apply'])) {
                    continue;
                }
                foreach ($config['rules'] as $k => $v) {
                    foreach ($strategy->data as $kv => $datum) {
                        if ($v['duration'] == 0) {
                            if (isset($v['condition'])) {
                                if (empty($arr)) {
                                    if ($v['field'] == $datum['key']) {
                                        $arr[] = "(" . $datum['dbl_v'] . $v['condition'] . $v['value'] . ")";
                                    }
                                } else {
                                    if ($v['field'] == $datum['key']) {
                                        $arr[] = array_pop($arr) . $v['operator'] . "(" . $datum['dbl_v'] . $v['condition'] . $v['value'] . ")";
                                    }
                                }
                            } else {
                                //switch
                                if (empty($arr)) {
                                    if ($v['field'] == $datum['key']) {
                                        $arr[] = $v['value'] == $datum['dbl_v'];
                                    }
                                } else {
                                    if ($v['field'] == $datum['key']) {
                                        $arr[] = array_pop($arr) . $v['operator'] . $v['value'] == $datum['dbl_v'];
                                    }
                                }
                            }
                        } else {
                            if (isset($v['condition'])) {
                                $arr[] = array_pop($arr) . $v['operator'] . true;
                            } else {
                                if ($v['value'] == $datum['dbl_v'] && $v['field'] == $datum['key']) {
                                    //开关
                                    if ($v['value'] == 0) {
                                        $kvStart = Kv::orderBy('ts', 'desc')->where(['key' => $v['field'], 'entity_id' => $v['device_id'], 'dbl_v' => 1])->first();
                                    } else {
                                        $kvStart = Kv::orderBy('ts', 'desc')->where(['key' => $v['field'], 'entity_id' => $v['device_id'], 'dbl_v' => 0])->first();
                                    }
                                    $minute = intval(($datum['ts'] - $kvStart['ts']) / 60000);
                                    if (empty($arr)) {
                                        $arr[] = $minute > $v['duration'];
                                    } else {
                                        $arr[] = array_pop($arr) . $v['operator'] . $minute > $v['duration'];
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($arr)) {
                    $conditionEval = array_pop($arr);
                    if (eval("return {$conditionEval};")) {
                        foreach ($config['apply'] as $config => $apply) {
                            if (!in_array($apply['field'], $fieldArr)) {
                                $fieldArr[] = $apply['field'];
                                $applyArr[] = $apply;
                            }
                        }
                    }
                }
            }
            $condition->ApplyResult($applyArr);
        } catch (\Exception $e) {
            Log::error('event strategy command', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
