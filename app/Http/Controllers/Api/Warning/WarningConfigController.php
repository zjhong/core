<?php


namespace App\Http\Controllers\Api\Warning;


use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Device;
use App\Models\Warning\WarningConfig;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WarningConfigController extends Controller
{
    //查询传感器下的字段
    public function field(){
        $result = [];
        $validator = Validator::make(request()->all(), [
            'bid' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $res = $this->read_static_cache();
        foreach ($res as $k => $v){
            $widget = Device::where('id',$post['bid'])->first();
            if($k == $widget['type']){
                foreach ($v['widgets'] as $key => $widget) {
                    if(isset($widget['fields'])){
                        foreach ($widget['fields'] as $field_key => $field){
                            if(!isset($field['type']) || $field['type'] == 1){
                                $result[$field_key]['key'] = $field_key;
                                $result[$field_key]['name'] = is_array($field)?$field['name']:$field;
                            }
                        }
                    }
                }
            }
        }
        $result = array_values($result);
        return $this->jsonResponse(200, 'success', $result);
    }

    public function show(){
        $validator = Validator::make(request()->all(), [
            'wid' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        try{
            $config = new WarningConfig();
            $limit     = 30;
            if (isset($post['wid'])) {
                $config = $config->where('wid', $post['wid']);
            }
            if (isset($post['limit']) && is_numeric($post['limit'])) {
                $limit = $post['limit'];
            }
            $config = $config->paginate($limit);
            foreach ($config as $key => $value){
                $config[$key]['config'] = json_decode($value['config'],true);
            }
            return $this->jsonResponse(200, 'success', $config);
        } catch (\Exception $e) {
            return $this->jsonResponse(500, 'error',[$e->getMessage()]);
        }
    }

    public function add(){
        $validator = Validator::make(request()->all(), [
            'wid' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        DB::beginTransaction();
        try {
            $switch = WarningConfig::where(['wid' => $post['wid'],'bid' => $post['bid']])->first();
            if(empty($switch)){
                $config = [
                    'id' => \Faker\Provider\Uuid::uuid(),
                    'wid' => $post['wid'],
                    'bid' => $post['bid'],
                    'sensor' => $post['sensor'],
                    'message' => $post['message'],
                    'name' => $post['name'],
                    'describe' => $post['describe'],
                    'config' => json_encode($post['config']),
                ];
                $data = WarningConfig::insertGetId($config);
            }else{
                return $this->jsonResponse('201','此设备已有预警策略');
            }
            DB::commit();
            return $this->jsonResponse('200','success',$data);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function update(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $data = WarningConfig::where('id',$post['id'])->first();
        return $this->jsonResponse('200','success',$data);
    }

    public function edit(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $config = [
            'name' => $post['name'],
            'describe' => $post['describe'],
            'bid' => $post['bid'],
            'sensor' => $post['sensor'],
            'message' => $post['message'],
            'config' => json_encode($post['config']),
        ];
        $data = WarningConfig::where('id',$post['id'])->update($config);
        return $this->jsonResponse('200','success',$data);
    }

    public function delete(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $data = WarningConfig::where('id',$post['id'])->delete();
        return $this->jsonResponse('200','success',$data);
    }
}
