<?php

namespace App\Console\Commands;

use App\Facades\Telemetry;
use App\Models\Automation\Conditions;
use App\Models\Assets\Device;
use App\Models\Telemetry\Kv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TimeStrategy extends Command
{
    const time = 2;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'time:strategy';

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
        $this->info(date('[Y-m-d H:i:s]') . ' time:strategy start');
        date_default_timezone_set('PRC');
        $Hour = date('H:i', time());
        $Day = date('Y/n/j H:i', time());
        $condition = new Conditions();
        try {
            $timeData = Conditions::OrderBy('sort', 'asc')->where(['type' => self::time, 'status' => 1])->get();
            foreach ($timeData as $key => $value) {
                $config = json_decode($value['config'], true);
                foreach ($config['rules'] as $k => $v){
                    if($v['interval'] == 1){
                        if ($Hour == $v['time']) {
                            $condition->ApplyResult($config['apply']);
                            Log::info(date('Y-m-d H:i:s') . 'Hour time:strategy end', ['data'=> []]);
                        }
                    }else{
                        if ($Day == $v['time']) {
                            $condition->ApplyResult($config['apply']);
                            Log::info(date('Y-m-d H:i:s') . 'Day time:strategy end', ['data'=> []]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('time:strategy command', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
