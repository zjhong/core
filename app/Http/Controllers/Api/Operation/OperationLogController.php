<?php


namespace App\Http\Controllers\Api\Operation;


use App\Http\Controllers\Api\Controller;
use App\Models\OperationLog\OperationLog;
use App\Models\Warning\Warning;

class OperationLogController extends Controller
{
    public function index(){
        date_default_timezone_set('PRC');
        $result = OperationLog::OrderBy('created_at','desc')->limit(100)->get();
        foreach ($result as $key => $value){
            $result[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
        }
        return $this->jsonResponse(200, 'success', $result);
    }

    public function list(){
        date_default_timezone_set('PRC');
        $post      = request()->all();
        $operationLog = new OperationLog();
        $limit     = 10;
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        if (isset($post['start_date'])) {
            $operationLog = $operationLog->where('created_at','>=',strtotime($post['start_date']));
        }
        if (isset($post['end_date'])) {
            $operationLog = $operationLog->where('created_at','<=',strtotime($post['end_date']));
        }
        $operationLog = $operationLog->orderBy('created_at','desc')->paginate($limit);
        foreach ($operationLog as $key => $value){
            $operationLog[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
        }
        return $this->jsonResponse(200, 'success', $operationLog);
    }
}
