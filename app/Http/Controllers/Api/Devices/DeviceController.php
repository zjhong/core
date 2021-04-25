<?php

namespace App\Http\Controllers\Api\Devices;

use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Device;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DeviceController extends Controller
{
    public function index(Request $request)
    {
        return 'test devices';
    }

    public function edit(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        DB::beginTransaction();
        try {
            $token = [
                'token' => $post['token']
            ];
            $data = Device::where('id', $post['id'])->update($token);
            DB::commit();
            return $this->jsonResponse('200', 'success', $data);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonResponse('500', '插入失败', $e->getMessage());
        }
    }
}
