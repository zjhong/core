<?php


namespace App\Http\Controllers\Api\Production;


use App\Http\Controllers\Api\Controller;
use App\Models\Production\Production;
use Illuminate\Support\Facades\Validator;

class ProductionController extends Controller
{
    public function index(){
        $post      = request()->all();
        $Production = new Production();
        $limit     = 30;
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        $Production = $Production->paginate($limit);
        foreach ($Production as $key => $value){
            $Production[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
            $Production[$key]['insert_at'] = date('Y-m-d H:i:s',$value['insert_at']);
            $Production[$key]['type'] = (new Production())->TYPE[$value['type']];
        }
        return $this->jsonResponse(200, 'success', $Production);
    }

    public function add(){
        $validator = Validator::make(request()->all(), [
            'type' => ['required'],
            'name' => ['required'],
            'value' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $arr = [
            'id' => \Faker\Provider\Uuid::uuid(),
            'name' => $post['name'],
            'type' => $post['type'],
            'value' => $post['value'],
            'remark' => $post['remark'],
            'created_at' => strtotime($post['created_at']),
            'insert_at' => time(),
        ];
        Production::insertGetId($arr);
        return $this->jsonResponse(200, 'success', []);
    }

    public function update(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $data = Production::where('id',$post['id'])->first();
        $data['insert_at'] = date('Y-m-d H:i:s',$data['insert_at']);
        return $this->jsonResponse(200, 'success', $data);
    }

    public function edit(){
        $validator = Validator::make(request()->all(), [
            'id' => ['required'],
            'type' => ['required'],
            'name' => ['required'],
            'value' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $arr = [
            'name' => $post['name'],
            'type' => $post['type'],
            'value' => $post['value'],
            'remark' => $post['remark'],
            'created_at' => strtotime($post['created_at']),
            'insert_at' => time(),
        ];
        Production::where('id',$post['id'])->update($arr);
        return $this->jsonResponse(200, 'success', []);
    }

    public function delete(){
        if (!request()->has('id')) {
            return $this->jsonResponse(2012, 'not found id, mast post id');
        }
        $post     = request()->all();
        Production::where('id',$post['id'])->delete();
        return $this->jsonResponse(200, 'success');
    }
}
