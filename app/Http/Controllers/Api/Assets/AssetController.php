<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Asset;
use App\Models\Assets\Device;
use App\Models\FieldMapping\FieldMapping;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    protected $asset;
    protected $device;

    public function __construct(Request $request, Asset $asset, Device $device)
    {
        $this->asset = $asset;
        $this->device = $device;

        parent::__construct($request);
    }

    /**
     * Get Asset List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = [];
        $res = $this->read_static_cache();
        if (!empty($res)) {
            foreach ($res as $key => $value){
                $result[$key]['id'] = $key;
                $result[$key]['name'] = $value['name'];
            }
            $arr_new = array_values($result);
            return $this->jsonResponse(200, 'success', $arr_new);
        } else {
            return $this->jsonResponse(500, '配置文件为空', array());
        }
    }

    /**
     * Add Asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
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
            foreach ($arr as $key => $value){
                $one_asset = $this->DealWithAssets($value['name'],1,0,$value['business_id']);
                if($one_asset){
                    if(isset($value['device'])){
                        $this->ProcessingEquipment($value['device'],$one_asset);
                    }
                    foreach ($value['two'] as $item) {
                        $two_asset = $this->DealWithAssets($item['name'],2,$one_asset,$item['business_id']);
                        if($two_asset){
                            if(isset($item['device'])){
                                $this->ProcessingEquipment($item['device'],$two_asset);
                            }
                            if(isset($item['there']) && !empty($item['there'])){
                                foreach ($item['there'] as $v){
                                    $there_asset = $this->DealWithAssets($v['name'],3,$two_asset,$v['business_id']);
                                    if($there_asset){
                                        if(isset($v['device'])){
                                            $this->ProcessingEquipment($v['device'],$there_asset);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return $this->jsonResponse('200','插入成功');
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    public function list(){
        if (!request()->has('business_id')) {
            return $this->jsonResponse(2012, 'not found id, mast post business_id');
        }
        $post  = request()->all();
        $asset_one = $this->asset->getWidgetDataLevelOne($post['business_id']);
        foreach ($asset_one as $key => $value){
            $asset_widget_one = $this->AccessToComponent($value->id);
            $asset_one[$key]->device = $asset_widget_one;
            $asset_two = $this->asset->getWidgetDataLevelTwo($value->id);
            foreach ($asset_two as $k => $v){
                $asset_widget_two = $this->AccessToComponent($v->id);
                $asset_two[$k]->device = $asset_widget_two;
                $asset_there = $this->asset->getWidgetDataLevelTwo($v->id);
                foreach ($asset_there as $ks => $vs){
                    $asset_widget_there =  $this->AccessToComponent($vs->id);
                    $asset_there[$ks]->device = $asset_widget_there;
                }
                $asset_two[$k]->there = $asset_there;
            }
            $asset_one[$key]->two = $asset_two;
        }
        return $this->jsonResponse(200, 'success', $asset_one);
    }

    /**
     * Edit Asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(){
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
            foreach ($arr as $key => $value){
                if(empty($value['id'])){
                    $one_asset = $this->DealWithAssets($value['name'],1,0,$value['business_id']);
                }else{
                    $this->asset->updateData($value['id'],['name' => $value['name']]);
                    $one_asset = $value['id'];
                }
                if($one_asset){
                    if(isset($value['device'])){
                        $this->DealingWithChange($value['device'],$one_asset);
                    }
                    foreach ($value['two'] as $item) {
                        if(empty($item['id'])) {
                            $two_asset = $this->DealWithAssets($item['name'],2,$one_asset,$item['business_id']);
                        }else{
                            $this->asset->updateData($item['id'],['name' => $item['name']]);
                            $two_asset = $item['id'];
                        }
                        //插入设备表
                        if($two_asset){
                            if(isset($item['device'])){
                                $this->DealingWithChange($item['device'],$two_asset);
                            }
                            if(isset($item['there']) && !empty($item['there'])){
                                foreach ($item['there'] as $v){
                                    if(empty($v['id'])) {
                                        $there_asset = $this->DealWithAssets($v['name'],3,$two_asset,$v['business_id']);
                                    }else{
                                        $this->asset->updateData($v['id'],['name' => $v['name']]);
                                        $there_asset = $v['id'];
                                    }
                                    if($there_asset) {
                                        if(isset($v['device'])) {
                                            $this->DealingWithChange($v['device'],$there_asset);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return $this->jsonResponse('200','插入成功');
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    /**
     * Delete Asset
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        $post     = request()->all();
        if($post['type'] == 1){
            $res = $this->asset->getWidgetDataLevelTwo(request('id'));
            if (!empty($res)) {
                return $this->jsonResponse(2014, '请删除下一级');
            }
            $this->asset->deleteData(request('id'));
            $DeviceData = Device::where('asset_id',request('id'))->get();
            foreach ($DeviceData as $val){
                Device::where('id',$val['id'])->delete();
                FieldMapping::where('device_id',$val['id'])->delete();
            }
        }else{
            $this->device->deleteData(request('id'));
            FieldMapping::where('device_id',request('id'))->delete();
        }
        return $this->jsonResponse(200, 'success');
    }

    /**
     * 处理添加资产
     * @return array
     */
    function DealWithAssets($name,$tier,$parent_id,$business_id){
        $Arr = [
            'id' => \Faker\Provider\Uuid::uuid(),
            'name' => $name,
            'tier' => $tier,
            'parent_id' => $parent_id,
            'business_id' => $business_id,
        ];
        //插入一级资产
        $asset = Asset::insertGetId($Arr);
        return $asset;
    }

    /**
     * 处理添加设备
     * @return array
     */
    function ProcessingEquipment($data,$asset_id){
        $result = '';
        foreach ($data as $device){
            $deviceArr = [
                'id' => \Faker\Provider\Uuid::uuid(),
                'asset_id' => $asset_id,
                'token' => md5(uniqid(microtime(true),true)),
                'type' => $device['type'],
                'name' => $device['name'],
                'extension' => 'Extensions',
            ];
            $result = Device::insertGetId($deviceArr);
            if(!empty($device['mapping'])){
                foreach ($device['mapping'] as $k => $v){
                    $fromArr = [
                        'id' => \Faker\Provider\Uuid::uuid(),
                        'device_id' => $result,
                        'field_from' => $v['field_from'],
                        'field_to' => $v['field_to'],
                    ];
                    FieldMapping::insertGetId($fromArr);
                }
            }
        }
        return $result;
    }

    /**
     * 处理修改设备
     * @return array
     */
    public function DealingWithChange($data,$asset_id){
        $resId = '';
        foreach ($data as  $deviceValue){
            if(empty($deviceValue['id'])) {
                $deviceValueOneArr = [
                    'id' => \Faker\Provider\Uuid::uuid(),
                    'asset_id' => $asset_id,
                    'token' => md5(uniqid(microtime(true),true)),
                    'type' => $deviceValue['type'],
                    'name' => $deviceValue['name'],
                    'extension' => 'Extensions',
                ];
                $resId = Device::insertGetId($deviceValueOneArr);
            }else{
                $this->device->updateDeviceData($deviceValue['id'],['name' => $deviceValue['name']]);
                $resId = $deviceValue['id'];
            }
            if(!empty($deviceValue['mapping'])){
                foreach ($deviceValue['mapping'] as $k => $val){
                    if(isset($val['id'])){
                        $fromArr = [
                            'field_from' => $val['field_from'],
                            'field_to' => $val['field_to'],
                        ];
                        FieldMapping::where('id',$val['id'])->update($fromArr);
                    }else{
                        $fromArr = [
                            'id' => \Faker\Provider\Uuid::uuid(),
                            'device_id' => $resId,
                            'field_from' => $val['field_from'],
                            'field_to' => $val['field_to'],
                        ];
                        FieldMapping::insertGetId($fromArr);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 获取图标组件列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function widget(){
        $result = [];
        if (!request()->has('id')) {
            return $this->jsonResponse(2012, 'not found id, mast post id');
        }
        $post = request()->all();
        $res = $this->read_static_cache();
        foreach ($res as $key => $val){
            if($key == $post['id']){
                foreach ($val['widgets'] as $k => $v){
                    $result[] = $v;
                }
            }
        }
        return $this->jsonResponse(200, 'success',$result);
    }

    /**
     * @return array
     */
    public function AccessToComponent($id){
        $asset_device = $this->device->getDeviceDataByID($id);
        foreach ($asset_device as $key => $value){
            $asset_device[$key]->disabled = $value->type == -1 ? false :true;
            $asset_device[$key]->dm = $value->type == -1 ? '' : '代码';
            $asset_device[$key]->state = $value->type == -1 ? '' : '正常';
            $res = $this->getDashBoard($value->type);
            $asset_device[$key]->dash = $res;
            $FieldMapping = FieldMapping::where('device_id',$value->id)->get();
            $asset_device[$key]->mapping = empty($FieldMapping) ? [] : $FieldMapping;
        }
        return $asset_device;
    }

    /**
     * 根据key获取图标组件列表
     * @return array
     */
    function getDashBoard($keyed){
        $result = [];
        $res = $this->read_static_cache();
        foreach ($res as $key => $val){
            if($key == $keyed){
                foreach ($val['widgets'] as $k => $v){
                    $result[] = $v;
                }
            }
        }
        return $result;
    }
}
