<?php

namespace App\Console\Commands;

use App\Models\Resources\Resources;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:resources';

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
        try {
            $data = [
                'id' => \Faker\Provider\Uuid::uuid(),
                'cpu' => $this->cpu_usage(),
                'mem' => $this->mem_usage(),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            Resources::insert($data);
        } catch (\Exception $e) {
            Log::error('get:resources command', [
            'message' => $e->getMessage(),
            ]);
        }
    }

    function cpu_usage(){
        $fp = popen('top -b -n 2 | grep -E "(Cpu\(s\))"', "r");
        $rs = '';
        while(!feof($fp)){
            $rs .= fread($fp, 1024);
        }
        $sys_info = explode("\n", $rs);
        $cpu_info = explode(",", $sys_info[1]);
        $cpu_usage = trim(trim($cpu_info[0], '%Cpu(s): '), 'us'); //百分比
        return $cpu_usage;
    }

    function mem_usage(){
        $fp = popen('top -b -n 2 | grep -E "(MiB Mem)"', "r");
        $rs = '';
        while(!feof($fp)){
            $rs .= fread($fp, 1024);
        }
        $sys_info = explode("\n", $rs);
        $mem_info = explode(",", $sys_info[1]);
        $mem_total = trim(trim($mem_info[0], 'KiB Mem : '), ' total');
        $mem_used = trim(trim($mem_info[2], 'used'));
        $mem_usage = round(100 * intval($mem_used) /     intval($mem_total), 2); //百分比
        return $mem_usage;
    }
}
