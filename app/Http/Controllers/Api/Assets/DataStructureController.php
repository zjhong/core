<?php


namespace App\Http\Controllers\Api\Assets;


use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Device;
use App\Models\FieldMapping\FieldMapping;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DataStructureController extends Controller
{
    public function add(){
        $validator = Validator::make(request()->all(), [
            'data' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $arr = json_decode($post['data'],true);
        DB::beginTransaction();
        try {
            foreach ($arr as $key => $val){
                $fromArr = [
                    'id' => \Faker\Provider\Uuid::uuid(),
                    'device_id' => $val['device_id'],
                    'field_from' => $val['field_from'],
                    'field_to' => $val['field_to'],
                ];
                FieldMapping::insertGetId($fromArr);
            }
            DB::commit();
            return $this->jsonResponse('200','插入成功');
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function list(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $res = FieldMapping::where('device_id',$post['id'])->get();
        return $this->jsonResponse('200','查询成功',$res);
    }

    public function update(){
        $validator = Validator::make(request()->all(), [
            'data' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $arr = json_decode($post['data'],true);
        DB::beginTransaction();
        try {
            foreach ($arr as $key => $val){
                if(isset($val['id'])){
                    $fromArr = [
                        'field_from' => $val['field_from'],
                        'field_to' => $val['field_to'],
                    ];
                    FieldMapping::where('id',$val['id'])->update($fromArr);
                }else{
                    $fromArr = [
                        'id' => \Faker\Provider\Uuid::uuid(),
                        'device_id' => $val['device_id'],
                        'field_from' => $val['field_from'],
                        'field_to' => $val['field_to'],
                    ];
                    FieldMapping::insertGetId($fromArr);
                }
            }
            DB::commit();
            return $this->jsonResponse('200','修改成功');
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function delete(){
        $post     = request()->all();
        FieldMapping::where('id',$post['id'])->delete();
        return $this->jsonResponse('200','删除成功');
    }

    public function field(){
        $res = [];
        $post     = request()->all();
        $read_static_cache = $this->read_static_cache();
        foreach ($read_static_cache as $key => $val){
            if (isset($post['field'])){
                if($key == $post['field']){
                    $res = $this->iconTreeComponent($val['widgets']);
                }
            }else{
                $res = array_merge($res,$this->iconTreeComponent($val['widgets']));
            }
        }
        return $this->jsonResponse(200, 'success',$res);
    }

    function iconTreeComponent($widgets){
        $arr = [];
        foreach ($widgets as $k => $v){
            $result = [];
            if(isset($v['fields'])){
                foreach ($v['fields'] as $field => $fieldVal){
                    $result[$field]['key'] = $field;
                    $result[$field]['name'] = is_array($fieldVal) ? $fieldVal['name'] : $fieldVal;
                    $result[$field]['type'] = is_array($fieldVal) ? $fieldVal['type'] : 1;
                }
                $arr[$k]['name'] = $v['name'];
                $arr[$k]['field'] = array_values($result);
            }
        }
        $arr = array_values($arr);
        return $arr;
    }
}
