<?php


namespace App\Http\Controllers\Api\Dashboards;


use App\Facades\Extension;
use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Asset;
use App\Models\Assets\Business;
use App\Models\Assets\Device;
use App\Models\Automation\Conditions;
use App\Models\Panels\Dashboard;
use App\Models\Panels\Widget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Get Dashboard List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $bashboardList = new Dashboard();
        $limit = 30;
        $bashboardList = $bashboardList->paginate($limit);
        return $this->jsonResponse(200, 'success', $bashboardList);
    }

    public function panelAdd(){
        $validator = Validator::make(request()->all(), [
            'title' => ['required', 'string'],
            'business_id' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $dashboard = [
                'id' => \Faker\Provider\Uuid::uuid(),
                'configuration' => '{"start_time":"2020-10-01T14:23","end_time":"2020-10-08T15:23","theme":1,"interval_time":0,"bg_theme":0}',
                'business_id' => $post['business_id'],
                'title' => $post['title'],
            ];
            $dashboardId = Dashboard::insertGetId($dashboard);
            DB::commit();
            return $this->jsonResponse('200','插入成功',$dashboardId);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function panelEdit(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'business_id' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $dashboard = [
                'business_id' => $post['business_id'],
                'title' => $post['title'],
            ];
            $data = Dashboard::where('id',$post['id'])->update($dashboard);
            DB::commit();
            return $this->jsonResponse('200','插入成功',$data);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function panelDelete(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        Widget::where('dashboard_id',$post['id'])->delete();
        Dashboard::where('id',$post['id'])->delete();
        return $this->jsonResponse('200','删除成功');
    }

    /**
     * Add Dashboard
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $validator = Validator::make(request()->all(), [
            'chart_id' => ['required', 'string'],
            'asset_id' => ['required', 'string'],
            'device_id' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $data = Widget::where('asset_id',$post['asset_id'])->where('widget_identifier',$post['widget_identifier'])->where('device_id',$post['device_id'])->where('dashboard_id',$post['chart_id'])->first();
            if(empty($data)){
                $widget = [
                    'id' => \Faker\Provider\Uuid::uuid(),
                    'dashboard_id' => $post['chart_id'],
                    'asset_id' => $post['asset_id'],
                    'config' => $this->dashboardConfig($post['chart_id'],$post['widget_identifier']),
                    'device_id' => $post['device_id'],
                    'widget_identifier' => $post['widget_identifier'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                Widget::insertGetId($widget);
            }else{
                $widget = [
                    'config' => $this->dashboardConfig($post['chart_id'],$post['widget_identifier']),
                    'widget_identifier' => $post['widget_identifier'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                Widget::where('asset_id',$post['asset_id'])->where('device_id',$post['device_id'])->where('dashboard_id',$post['chart_id'])->update($widget);
            }
            DB::commit();
            return $this->jsonResponse('200','插入成功');
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }


    public function list(){
        $post = request()->all();
        $dev = Widget::where('dashboard_id',$post['chart_id'])->get();
        return $this->jsonResponse('200','成功',$dev);
    }

    public function edit(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required', 'string'],
            'asset_id' => ['required', 'string'],
            'device_id' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $widget = [
                'device_id' => $post['device_id'],
                'asset_id' => $post['asset_id'],
                'widget_identifier' => $post['widget_identifier'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            Widget::where('id',$post['chart_id'])->insertGetId($widget);
            DB::commit();
            return $this->jsonResponse('200','插入成功');
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function delete(){
        $post = request()->all();
        Widget::where('id',$post['id'])->delete();
        return $this->jsonResponse('200','删除成功');
    }

    public function business()
    {
        $business = Business::get();
        return $this->jsonResponse(200, 'success', $business);
    }

    public function property()
    {
        $validator = Validator::make(request()->all(), [
            'wid' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $assets = Conditions::disposeProperty($post['wid']);
        return $this->jsonResponse(200, 'success', $assets);
    }

    public function device(){
        $validator = Validator::make(request()->all(), [
            'asset_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $assets = Device::where('asset_id',$post['asset_id'])->get();
        return $this->jsonResponse(200, 'success', $assets);
    }

    public function component(){
        $validator = Validator::make(request()->all(), [
            'device_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $result = $this->forIcon($post['device_id']);
        return $this->jsonResponse(200, 'success', $result);
    }

    //获取时间
    public function getTime()
    {
        $validator = Validator::make(request()->all(), [
            'chart_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $res = Dashboard::where('id', $post['chart_id'])->first();
        $res['config'] = json_decode($res['configuration']);
        return $this->jsonResponse(200, 'success', $res);
    }

    //插入筛选时间
    public function insertTime()
    {
        $validator = Validator::make(request()->all(), [
            'start_time' => ['required'],
            'end_time' => ['required'],
            'theme' => ['required'],
            'chart_id' => ['required'],
            'interval_time' => ['required'],
            'bg_theme' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $chart_id = $post['chart_id'];
        unset($post['chart_id']);
        $data = [
            'configuration' => json_encode($post),
        ];
        //查询最新的一条
        $res = Dashboard::where('id', $chart_id)->first();
        if (empty($res)) {
            $bashboard = Dashboard::create($data);
        } else {
            $bashboard = Dashboard::where('id', $res->id)->update($data);
        }
        return $this->jsonResponse(200, 'success', $bashboard);
    }

    public function dashboard(Request $request){
        $Arr = [];
        $validator = Validator::make(request()->all(), [
            'chart_id' => ['required']
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }

        $dashboard_id = $request->input('chart_id'); //dashboard id
        $widget = Widget::where('dashboard_id',$dashboard_id)->get();
        foreach ($widget as $key => $val){
            $Arr[] = array_merge($val->config,  ['id' => $val->id, 'fields' => Extension::getWidgetFields($val->widget_identifier)]);
        }
        return $this->jsonResponse(200, 'success', $Arr);
    }

    public function dashboard_copy(Request $request)
    {
        $Arr = [];
        $validator = Validator::make(request()->all(), [
            'bid' => ['required']
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }

        $aid = $request->input('aid'); //asset id
        $bid = $request->input('bid'); //business work id

        //get all sub assets
        $assetId = [];
        if ($request->input('aid')) {
            $assetId[] = $request->input('aid');
        }
        $recursive = function ($parent_id) use (&$assetId, &$recursive, $bid) {
            $assets = Asset::where('parent_id', $parent_id)->where('business_id', $bid)->get();
            if ($assets->count() > 0) {
                foreach ($assets as $asset) {
                    $assetId[] = $asset->id;
                    $recursive($asset->id);
                }
            }
        };
        $recursive($aid);

        //限制gps track到单个设备
        $ignoreDevices = [];
        if (Asset::where('parent_id', $aid)->where('business_id', $bid)->count() > 0) {
            $ignoreDevices[] = 'gps:track';
            $ignoreDevices[] = 'environmentpanel:normal';
            $ignoreDevices[] = 'waterfertilizermachinepanel:normal';
        }

        $widgetNames = Device::whereIn('asset_id', $assetId)->pluck('type')->toArray();
        $widgetNames = array_unique($widgetNames);

        $dashboard = Dashboard::where('business_id', $bid)->first();
        $dashboard_widgets = Widget::where('dashboard_id', $dashboard->id)->get();
        foreach ($dashboard_widgets as $k => $v) {
            if (!in_array($v->widget_identifier, $ignoreDevices) && in_array(strstr($v->widget_identifier, ':', true), $widgetNames)) {
                $Arr[] = array_merge($v->config,  ['id' => $v->id, 'fields' => Extension::getWidgetFields($v->widget_identifier)]);
            }
        }

        return $this->jsonResponse(200, 'success', $Arr);
    }

    public function updateDashboard(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
            'config' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }

        $widget = Widget::find($request->input('id'));
        $config = $widget->config;

        $replaceConfig = json_decode($request->input('config'), true);
        $replaceConfig = array_diff($replaceConfig, ['chart_type']);

        $config = array_merge($config, $replaceConfig);
        $res = Widget::where('id', $request->input('id'))->update([
            'config' => $config
        ]);

        return $this->jsonResponse(200, 'success', $res);
    }

    /**
     * @param $dashboard_id
     * @param $key
     * @return false|string
     */
    function dashboardConfig($dashboard_id, $key){
        $widget = Widget::where('dashboard_id',$dashboard_id)->count();
        $slice_id = $widget == 0 ? 1 : $widget + 1;
        $y = $widget == 0 ? 0 : $widget * 6;
        $extConfig = $this->read_static_cache();

        list($extensionName, $widgetName) = explode(':', $key);
        // get extension config
        $result = $extConfig[$extensionName]['widgets'][$widgetName];

        return json_encode([
            'slice_id' => $slice_id,
            'x' => 0,
            'y' => $y,
            'w' => '12',
            'h' => '6',
            'width' => '360',
            'height' => '210',
            'i' => md5(uniqid(microtime())),
            'chart_type' => Extension::getTemplateName($extensionName, $result['template']),
            'title' => $result['name'],
        ]);
    }

    function forIcon($device_id){
        $read_static_cache = $this->read_static_cache();
        $result = [];
        $device = Device::where('id',$device_id)->select('id','type','extension')->get();
        $device = json_decode(json_encode($device,JSON_UNESCAPED_UNICODE),true);
        foreach ($device as $type) {
            foreach ($read_static_cache as $key => $val) {
                if ($key == $type['type']) {
                    foreach ($val['widgets'] as $k => $v) {
                        $result[$k]['thumbnail'] = app_path().'/'.$type['extension'].'/'.$key.$v['thumbnail'];
                        $result[$k]['name'] = $v['name'];
                        $result[$k]['key'] = $key . ':' . $k;
                    }
                }
            }
        }
        $result = array_values($result);
        return $result;
    }

    public function realTime()
    {
        date_default_timezone_set('PRC');
        $validator = Validator::make(request()->all(), [
            'type' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $data['end_time'] = date('Y-m-d H:i:s');
        if ($post['type'] == 1) {
            //十五分钟内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 900);
        } elseif ($post['type'] == 2) {
            //三十分钟内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 1800);
        } elseif ($post['type'] == 3) {
            //一小时内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 3600);
        } elseif ($post['type'] == 4) {
            //三小时内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 3600 * 3);
        } elseif ($post['type'] == 5) {
            //六小时内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 3600 * 6);
        } elseif ($post['type'] == 6) {
            //十二小时内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 3600 * 12);
        } elseif ($post['type'] == 7) {
            //二十四小时内
            $data['start_time'] = date("Y-m-d H:i:s", strtotime($data['end_time']) - 3600 * 24);
        }
        return $this->jsonResponse(200, 'success', $data);
    }
}
