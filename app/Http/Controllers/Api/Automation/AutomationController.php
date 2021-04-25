<?php


namespace App\Http\Controllers\Api\Automation;


use App\Facades\Telemetry;
use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Asset;
use App\Models\Automation\Conditions;
use App\Models\Assets\Device;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AutomationController extends Controller
{
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            'business_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $assetSwitch = new Conditions();
        $limit = 30;
        if (isset($post['business_id'])) {
            $assetSwitch = $assetSwitch->where('business_id', $post['business_id']);
        }
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        $assetSwitch = $assetSwitch->paginate($limit);
        foreach ($assetSwitch as $key => $val) {
            $assetSwitch[$key]['config'] = json_decode($val['config']);
        }
        return $this->jsonResponse(200, 'success', $assetSwitch);
    }

    public function add()
    {
        $validator = Validator::make(request()->all(), [
            'business_id' => ['required'],
            'status' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $switch = [
                'id' => \Faker\Provider\Uuid::uuid(),
                'business_id' => $post['business_id'],
                'name' => $post['name'],
                'describe' => $post['describe'],
                'status' => $post['status'],
                'config' => json_encode($post['config']),
                'sort' => $post['sort'],
                'type' => $post['type'],
                'issued' => $post['issued'],
            ];
            $data = Conditions::insertGetId($switch);
            if ($data && $post['issued'] == 1) {
                $config = $post['config'];
                if (!empty($config)) {
                    $this->ApplyEcPh($config);
                }
            }
            DB::commit();
            return $this->jsonResponse('200', 'success', $data);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500', '插入失败', $e->getMessage());
        }
    }

    public function show()
    {
        $validator = Validator::make(request()->all(), [
            'bid' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $result = $this->AcquisitionConditions($post['bid'], 'condition');
        return $this->jsonResponse('200', 'success', $result);
    }

    public function status()
    {
        $result = [];
        $data = (new Conditions())->StatusArr;
        foreach ($data as $key => $datum) {
            $result[$key]['id'] = $key;
            $result[$key]['name'] = $datum;
        }
        return $this->jsonResponse('200', 'success', $result);
    }

    public function symbol()
    {
        $data = (new Conditions())->symbolArr;
        return $this->jsonResponse('200', 'success', $data);
    }

    public function update()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $assetsSwitch = Conditions::where('id', $post['id'])->first();
        $read_static_cache = $this->read_static_cache();
        $assetsSwitch['config'] = json_decode($assetsSwitch['config'], true);
        $assetsSwitch = json_decode(json_encode($assetsSwitch, JSON_UNESCAPED_UNICODE), true);
        $deviceData = $this->getDevice($assetsSwitch['business_id']);
        if ($assetsSwitch['type'] == 1) {
            foreach ($assetsSwitch['config']['rules'] as $rule => $switch) {
                $assetsSwitch['config']['rules'][$rule]['device'] = $deviceData;
                $assetsSwitch['config']['rules'][$rule]['assemblyArr'] = $this->AcquisitionConditions($switch['device_id'], 'condition');
                $assetsSwitch['config']['rules'][$rule]['conditionArr'] = Conditions::disposeComponent($read_static_cache, $switch['asset_id']);
            }
        }

        foreach ($assetsSwitch['config']['apply'] as $config => $apply) {
            $assetsSwitch['config']['apply'][$config]['device'] = $deviceData;
            $assetsSwitch['config']['apply'][$config]['switch'] = $this->AcquisitionConditions($apply['device_id'], 'switch');
            $assetsSwitch['config']['apply'][$config]['conditionArr'] = Conditions::disposeComponent($read_static_cache, $apply['asset_id']);
        }
        return $this->jsonResponse('200', 'success', $assetsSwitch);
    }

    public function edit()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $switch = [
                'name' => $post['name'],
                'describe' => $post['describe'],
                'status' => $post['status'],
                'config' => json_encode($post['config']),
                'sort' => $post['sort'],
                'type' => $post['type'],
                'issued' => $post['issued'],
            ];
            $data = Conditions::where('id', $post['id'])->update($switch);
            if ($data && $post['issued'] == 1) {
                $config = $post['config'];
                if (!empty($config)) {
                    $this->ApplyEcPh($config);
                }
            }
            DB::commit();
            return $this->jsonResponse('200', 'success', $data);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500', '插入失败', $e->getMessage());
        }
    }

    public function delete()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            //删除策略
            Conditions::where('id', $post['id'])->delete();
            DB::commit();
            return $this->jsonResponse('200', 'success', []);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500', '插入失败', $e->getMessage());
        }
    }

    public function property()
    {
        $validator = Validator::make(request()->all(), [
            'business_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(3011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $result = $this->getDevice($post['business_id']);
        return $this->jsonResponse(200, 'success', $result);
    }

    public function instruct()
    {
        $validator = Validator::make(request()->all(), [
            'bid' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $result = $this->AcquisitionConditions($post['bid'], 'switch');
        return $this->jsonResponse(200, 'success', $result);
    }


    function AcquisitionConditions($device_id, $conditions)
    {
        $result = [];
        $res = $this->read_static_cache();
        foreach ($res as $k => $v) {
            $widget = Device::where('id', $device_id)->first();
            if ($k == $widget['type']) {
                foreach ($v['widgets'] as $key => $widget) {
                    if (isset($widget['fields'])) {
                        foreach ($widget['fields'] as $field_key => $field) {
                            if ($conditions == 'condition') {
                                if (isset($field['type']) && ($field['type'] != 4 && $field['type'] != 5)) {
                                    $result[$field_key]['key'] = $field_key;
                                    $result[$field_key]['name'] = is_array($field) ? $field['name'] : $field;
                                    $result[$field_key]['type'] = isset($field['type']) ? $field['type'] : 1;
                                    $result[$field_key]['symbol'] = isset($field['symbol']) ? $field['symbol'] : '';
                                }
                            } else {
                                if (isset($field['type']) && ($field['type'] != 1 && $field['type'] != 4 && $field['type'] != 5)) {
                                    $result[$field_key]['key'] = $field_key;
                                    $result[$field_key]['name'] = is_array($field) ? $field['name'] : $field;
                                    $result[$field_key]['type'] = isset($field['type']) ? $field['type'] : 1;
                                    $result[$field_key]['symbol'] = isset($field['symbol']) ? $field['symbol'] : '';
                                }
                            }
                        }
                    }
                }
            }
        }
        $result = array_values($result);
        return $result;
    }

    function getDevice($wid)
    {
        $asset = new Asset();
        $result = $asset::where('tier', 1)->where('business_id', $wid)->get();
        foreach ($result as $key => &$val) {
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
        return $result;
    }

    function ApplyEcPh($config)
    {
        $arr = [];
        $device_id = '';
        if(count($config['rules']) == 1 && count($config['apply']) == 1){
            foreach ($config['rules'] as $key => $val){
                $arr['rules']['field'] = $val['field'];
                $arr['rules']['condition'] = $val['condition'];
                $arr['rules']['value'] = $val['value'];
            }
            foreach ($config['apply'] as $k => $v){
                $arr['apply'][$v['field']] = $v['value'];
                $device_id = $v['device_id'];
            }
            $token = Device::getTokenByDeviceId($device_id);
            Telemetry::sendDataToClient($token, $arr);
            Log::info('Automation:strategy ph ec data', ['token' => $token, 'data' => $arr]);
        }
    }
}
