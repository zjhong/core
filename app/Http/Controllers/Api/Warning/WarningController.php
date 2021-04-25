<?php


namespace App\Http\Controllers\Api\Warning;


use App\Http\Controllers\Api\Controller;
use App\Models\Warning\Warning;

class WarningController extends Controller
{
    public function index(){
        date_default_timezone_set('PRC');
        $result = Warning::OrderBy('created_at','desc')->limit(100)->get();
        foreach ($result as $key => $value){
            $result[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
        }
        return $this->jsonResponse(200, 'success', $result);
    }

    public function list(){
        date_default_timezone_set('PRC');
        $post      = request()->all();
        $warning = new Warning();
        $limit     = 10;
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        if (isset($post['start_date'])) {
            $warning = $warning->where('created_at','>=',strtotime($post['start_date']));
        }
        if (isset($post['end_date'])) {
            $warning = $warning->where('created_at','<=',strtotime($post['end_date']));
        }
        $warning = $warning->orderBy('created_at','desc')->paginate($limit);
        foreach ($warning as $key => $value){
            $warning[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
        }
        return $this->jsonResponse(200, 'success', $warning);
    }
}
