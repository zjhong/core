<?php


namespace App\Http\Controllers\Api\Navigation;


use App\Http\Controllers\Api\Controller;
use App\Models\Assets\Business;
use App\Models\Automation\Conditions;
use App\Models\Navigation\Navigation;
use App\Models\Panels\Dashboard;
use App\Models\Warning\WarningConfig;
use Illuminate\Support\Facades\Validator;

class NavigationController extends Controller
{
    public function add(){
        $validator = Validator::make(request()->all(), [
            'type' => ['required'],
            'name' => ['required'],
            'data' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(2011, 'error', [], $validator->errors());
        }
        $post      = request()->all();
        $data = Navigation::where('type',$post['type'])->where('name',$post['name'])->where('data',$post['data'])->first();
        if(empty($data)){
            $post['id'] = \Faker\Provider\Uuid::uuid();
            Navigation::insertGetId($post);
        }else{
            $post['count'] = $data['count'] + 1;
            Navigation::where('id',$data['id'])->update($post);
        }
        return $this->jsonResponse(200, 'success', []);
    }

    public function list(){
        $arr = [];
        $data = Navigation::OrderBy('count','desc')->get();
        if(!empty($data)){
            foreach ($data as $key => $value){
                $value['data'] = json_decode($value['data'],true);
                if(count($arr) < 6){
                    if($value['type'] == 1 || $value['type'] == 2){
                        $result = Business::where('id',$value['data']['id'])->first();
                        if(!empty($result)){
                            $arr[] = $value;
                        }else{
                            Navigation::where('id',$value['id'])->delete();
                        }
                    }elseif ($value['type'] == 3){
                        $result = WarningConfig::where('wid',$value['data']['id'])->first();
                        if(!empty($result)){
                            $arr[] = $value;
                        }else{
                            Navigation::where('id',$value['id'])->delete();
                        }
                    }elseif ($value['type'] == 4){
                        $result = Dashboard::where('business_id',$value['data']['business_id'])->where('id',$value['data']['chart_id'])->first();
                        if(!empty($result)){
                            $arr[] = $value;
                        }else{
                            Navigation::where('id',$value['id'])->delete();
                        }
                    }
                }
            }
        }
        return $this->jsonResponse(200, 'success', $arr);
    }
}
