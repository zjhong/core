<?php


namespace App\Http\Controllers\Api\Home;


use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Asset;
use App\Models\Assets\Business;
use App\Models\Assets\Device;
use App\Models\Automation\Conditions;
use App\Models\Panels\Dashboard;
use App\Models\Resources\Resources;
use App\Models\Telemetry\Kv;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'business' => Business::count(),//业务
            'assets' => Asset::count(),//资产
            'equipment' => Device::count(),//设备
            'dashboard' => Dashboard::count(),//可视化
            'conditions' => Conditions::count(),//策略
        ];
        return $this->jsonResponse('200', 'success', $data);
    }

    public function show()
    {
        $validator = Validator::make(request()->all(), [
            'did' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $Credentials = Device::where('id', $post['did'])->first();
        return $this->jsonResponse('200', 'success', $Credentials);
    }

    public function list(){
        $resources = Resources::orderBy('created_at','desc')->first();
        $data['cpu_usage'] = empty($resources['cpu']) ? 0 : $resources['cpu'];
        $data['mem_usage'] = empty($resources['mem']) ? 0 : $resources['mem'];
        $data['msg'] = Kv::count();
        $data['device'] = Device::count();
        return $this->jsonResponse('200', 'success', $data);
    }

    public function chart(){
        $resources = [
            'cpu' => Resources::getNewResource('cpu'),
            'mem' => Resources::getNewResource('mem'),
        ];
        foreach ($resources['cpu'] as $k => $v){
            $resources['cpu'][$k]->created_at = date('H:i',strtotime($v->created_at));
        }
        foreach ($resources['mem'] as $ks => $vs){
            $resources['mem'][$ks]->created_at = date('H:i',strtotime($vs->created_at));
        }
        return $this->jsonResponse('200', 'success', $resources);
    }
}

