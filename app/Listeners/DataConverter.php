<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DataConverter
{
    public $type = [
        'jcq' => [
            "jcq_sb",//水泵接触器已打开
            "jcq_zyw1p",//遮阳网1正转接触器已打开
            "jcq_zyw2p",//遮阳网2正转接触器已打开
            "jcq_zyw3p",//遮阳网3正转接触器已打开
            "jcq_zyw4p",//遮阳网4正转接触器已打开
            "jcq_zyw5p",//遮阳网5正转接触器已打开
            "jcq_zyw1n",//遮阳网1反转接触器已打开
            "jcq_zyw2n",//遮阳网2反转接触器已打开
            "jcq_zyw3n",//遮阳网3反转接触器已打开
            "jcq_zyw4n",//遮阳网4反转接触器已打开
            "jcq_zyw5n",//遮阳网5反转接触器已打开
            "jcq_fsl1",//风水帘1接触器已打开
            "jcq_fsl2",//风水帘2接触器已打开
            "jcq_fsl3",//风水帘3接触器已打开
            "jcq_fsl4",//风水帘4接触器已打开
            "jcq_fsl5",//风水帘5接触器已打开
            "jcq_fsl6",//风水帘6接触器已打开
            "jcq_fsl7",//风水帘7接触器已打开
        ],
        'jcq_err' => [
            "jcq_sb_err",//水泵接触器状态报警
            "jcq_zyw1p_err",//遮阳网1正转接触器状态报警
            "jcq_zyw2p_err",//遮阳网2正转接触器状态报警
            "jcq_zyw3p_err",//遮阳网3正转接触器状态报警
            "jcq_zyw4p_err",//遮阳网4正转接触器状态报警
            "jcq_zyw5p_err",//遮阳网5正转接触器状态报警
            "jcq_zyw1n_err",//遮阳网1反转接触器状态报警
            "jcq_zyw2n_err",//遮阳网2反转接触器状态报警
            "jcq_zyw3n_err",//遮阳网3反转接触器状态报警
            "jcq_zyw4n_err",//遮阳网4反转接触器状态报警
            "jcq_zyw5n_err",//遮阳网5反转接触器状态报警
            "jcq_fsl1_err",//风水帘1接触器状态报警
            "jcq_fsl2_err",//风水帘2接触器状态报警
            "jcq_fsl3_err",//风水帘3接触器状态报警
            "jcq_fsl4_err",//风水帘4接触器状态报警
            "jcq_fsl5_err",//风水帘5接触器状态报警
            "jcq_fsl6_err",//风水帘6接触器状态报警
            "jcq_fsl7_err",//风水帘7接触器状态报警
        ],
        'sw' => [
            "sw_m",//主手动自动控制状态
            "sw_sb",//水泵手动自动控制状态
            "sw_zyw1",//遮阳网1手动自动控制状态
            "sw_ zyw2",// 遮阳网2手动自动控制状态
            "sw_ zyw3",// 遮阳网3手动自动控制状态
            "sw_ zyw4",// 遮阳网4手动自动控制状态
            "sw_ zyw5",// 遮阳网5手动自动控制状态
            "sw_fsl1",//风水帘1手动自动控制状态
            "sw_ fsl2",// 风水帘2手动自动控制状态
            "sw_ fsl3",// 风水帘3手动自动控制状态
            "sw_ fsl4",// 风水帘4手动自动控制状态
            "sw_ fsl5",// 风水帘5手动自动控制状态
            "sw_ fsl6",// 风水帘6手动自动控制状态
            "sw_ fsl7",// 风水帘7手动自动控制状态
        ],
        'sf' => [
            "liquid_level1",
            "liquid_level2",
            "liquid_level3",
            "liquid_level4",
            "liquid_level5",
            "water_pressure",
            "dissolved_oxygen",
            "ph",
            "ec",
            "solenoid_valve1",
            "solenoid_valve2",
            "solenoid_valve3",
            "solenoid_valve4",
            "solenoid_valve5",
            "solenoid_valve6",
            "metering_pump1",
            "metering_pump2",
            "metering_pump3",
            "metering_pump4",
            "peristaltic_pump1",
            "peristaltic_pump2",
            "peristaltic_pump3",
            "peristaltic_pump4",
            "auto_switch"
        ],
        'by'=> [
            "jcq_by1",//备用
            "jcq_by2",//备用
            "jcq_by3",//备用
            "jcq_by4",//备用
            "jcq_by5",//备用
            "jcq_by6",//备用
            "jcq_by7",//备用
            "jcq_by8",//备用
            "jcq_by9",//备用
            "jcq_by10",//备用
            "jcq_by11",//备用
            "jcq_by12",//备用
            "jcq_by13",//备用
        ],
        'ye'=>[
            "liquid_level1",//液位
            "temperature1",//温度
            "PH1",//PH1
            "PH2",//PH2
            "PH3",//PH3
            "PH4",//PH4
            "Dissolved_Oxygen1",//溶解氧1
            "Dissolved_Oxygen2",//溶解氧2
            "Dissolved_Oxygen3",//溶解氧3
            "Dissolved_Oxygen4",//溶解氧4
            "ammonia_nitrogen1",//氨氮1
            "ammonia_nitrogen2",//氨氮2
            "ammonia_nitrogen3",//氨氮3
            "ammonia_nitrogen4",//氨氮4
        ]
    ];

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
     * @param \App\Events\Telemetry $event
     * @return void
     */
    public function handle(\App\Events\Telemetry $event)
    {
        try {
            $data = json_decode($event->data, true);
            if (isset($data['token']) && isset($data['values'])) {
                if (isset($data['type']) && isset($this->type[$data['type']])) {
                    Log::debug('DataConverter processing', [$data['type']]);
                    //把values转换为具体字段
                    foreach ($data['values'] as $key => $val) {
                        if (isset($this->type[$data['type']][$key])) {
                            unset($data['values'][$key]);
                            $data['values'][$this->type[$data['type']][$key]] = $val;
                        }
                    }
                    $event->data = json_encode($data);
                }
            }
        } catch (\Exception $e) {
            Log::error('DataConverter handle error', [$e->getCode(), $e->getMessage()]);
        }
    }
}
