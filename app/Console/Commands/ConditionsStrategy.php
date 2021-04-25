<?php

namespace App\Console\Commands;

use App\Facades\Telemetry;
use App\Models\Automation\Conditions;
use App\Models\Assets\Device;
use App\Models\Telemetry\Kv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConditionsStrategy extends Command
{
    const condition = 1;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conditions:strategy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(date('[Y-m-d H:i:s]') . ' conditions:strategy start');
        try {
            $applyArr = [];
            $fieldArr = [];
            $condition = new Conditions();
            //Get open conditional policies according to priority
            $conditionsData = Conditions::OrderBy('sort', 'asc')->where(['type' => self::condition, 'status' => 1])->get();
            foreach ($conditionsData as $key => $value) {
                $arr = [];
                $config = json_decode($value['config'], true);
                if (empty($config['apply'])) {
                    continue;
                }
                foreach ($config['rules'] as $k => $v) {
                    //get latest data
                    $kv = Kv::orderBy('ts', 'desc')->where(['key' => $v['field'], 'entity_id' => $v['device_id']])->first();
                    if ($v['duration'] == 0) {
                        //time is 0
                        if (isset($v['condition'])) {
                            //input value
                            if (empty($arr)) {
                                if ($kv['key'] == $v['field']) {
                                    $arr[] = "(" . $kv['dbl_v'] . $v['condition'] . $v['value'] . ")";
                                }
                            } else {
                                if ($kv['key'] == $v['field']) {
                                    $arr[] = array_pop($arr) . $v['operator'] . "(" . $kv['dbl_v'] . $v['condition'] . $v['value'] . ")";
                                }
                            }
                        } else {
                            //switch
                            if (empty($arr)) {
                                if ($kv['key'] == $v['field']) {
                                    $arr[] = $v['value'] == $kv['dbl_v'];
                                }
                            } else {
                                if ($kv['key'] == $v['field']) {
                                    $arr[] = array_pop($arr) . $v['operator'] . $v['value'] == $kv['dbl_v'];
                                }
                            }
                        }
                    } else {
                        if (isset($v['condition'])) {
                            $arr[] = array_pop($arr) . $v['operator'] . true;
                        } else {
                            if ($v['value'] == $kv['dbl_v'] && $kv['key'] == $v['field']) {
                                //switch
                                if ($v['value'] == 0) {
                                    $kvStart = Kv::orderBy('ts', 'desc')->where(['key' => $v['field'], 'entity_id' => $v['device_id'], 'dbl_v' => 1])->first();
                                } else {
                                    $kvStart = Kv::orderBy('ts', 'desc')->where(['key' => $v['field'], 'entity_id' => $v['device_id'], 'dbl_v' => 0])->first();
                                }
                                $minute = intval(($kv['ts'] - $kvStart['ts']) / 60000);
                                //the latest data
                                if (empty($arr)) {
                                    $arr[] = $minute > $v['duration'];
                                } else {
                                    $arr[] = array_pop($arr) . $v['operator'] . $minute > $v['duration'];
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
            Log::error('conditions:strategy command', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
