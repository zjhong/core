<?php


namespace App\Http\Controllers\Api\Assets;


use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Asset;
use App\Models\Assets\Business;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    /**
     * Get Business List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $post      = request()->all();
        $businessList = new Business();
        $limit     = 30;
        if (isset($post['name'])) {
            $businessList = $businessList->where('name', 'like', '%' . $post['name'] . '%');
        }
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        $businessList = $businessList->paginate($limit);
        foreach ($businessList as $key => $value){
            $businessList[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
            $asset = new Asset();
            $res = $asset->getDataById($value['id']);
            if(empty($res)){
                $businessList[$key]['is_device'] = 0;
            }else{
                $businessList[$key]['is_device'] = 1;
            }
        }
        return $this->jsonResponse(200, 'success', $businessList);
    }

    /**
     * Add Business
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $validator = Validator::make(request()->all(), [
            'name'        => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        DB::beginTransaction();
        try {
            $post     = request()->all();
            $post['created_at'] = strtotime(date('Y-m-d H:i:s'));
            $customer = Business::create($post);
            DB::commit();
            return $this->jsonResponse(200, 'success', $customer);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500','插入失败',$e->getMessage());
        }
    }

    /**
     * Edit Business
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(2012, 'not found id, mast post id');
        }
        $validator = Validator::make(request()->all(), [
            'name'        => ['string'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2013, 'error', [], $validator->errors());
        }
        $post  = request()->all();
        $asset = Business::find($post['id']);
        if (!$asset) {
            return $this->jsonResponse(2014, 'asset does not exist');
        }
        $asset = $asset->update($post);
        return $this->jsonResponse(200, 'success', $asset);
    }

    /**
     * Delete Business
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(2012, 'not found id, mast post id');
        }
        $asset = Business::find(request('id'));
        if (!$asset) {
            return $this->jsonResponse(2014, 'asset does not exist');
        }
        //查询是否存在资产
        $assets = Asset::where('business_id',request('id'))->first();
        if(!empty($assets)){
            return $this->jsonResponse(2016, '您的业务下存在资产，请先删除资产');
        }
        $asset->delete();
        return $this->jsonResponse(200, 'success');
    }
}
