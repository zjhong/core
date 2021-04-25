<?php


namespace App\Http\Controllers\Api\System;


use App\Http\Controllers\Api\Controller;
use App\Models\SystemConfig\SystemConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    public function index(){
        $validator = Validator::make(request()->all(), [
            'type' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $image = $this->applyImage(request()->file('image'));
        $sys = [
            'name' => $post['name'],
            'image' => $image,
            'copyright' => $post['copyright'],
        ];
        $data['id'] = \Faker\Provider\Uuid::uuid();
        $data['config'] = json_encode($sys);
        $data['type'] = $post['type'];
        $config = SystemConfig::where('type',$post['type'])->first();
        if(empty($config)){
            SystemConfig::insert($data);
        }else{
            SystemConfig::where('type',$post['type'])->update($data);
        }
        return $this->jsonResponse(200, 'success', []);
    }

    public function list(){
        $validator = Validator::make(request()->all(), [
            'type' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $config = SystemConfig::where('type',$post['type'])->first();
        $config = json_decode(json_encode($config,JSON_UNESCAPED_UNICODE),true);
        $config['config'] = json_decode($config['config'],true);
        $config['config']['image'] = Storage::url('photo').'/'.$config['config']['image'];
        return $this->jsonResponse(200, 'success', $config);
    }
}
