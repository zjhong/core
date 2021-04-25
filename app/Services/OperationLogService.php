<?php


namespace App\Services;


use Illuminate\Http\Request;

class OperationLogService
{
    /**
     * @param $type
     * @param $describe
     * @param $data_id
     * @return false|string
     */
    public function handle($type,$describe,$data_id)
    {
        if(empty($type) || empty($describe) || empty($data_id)){
            return json_encode(['data' => []]);
        }
        $request = Request::capture();
        $path = $request->path();
        $method = $request->method();
        $ip = $request->ip();
        $data = [
            'path' => $path,
            'method' => $method,
            'ip' => $ip,
        ];
        $result = \App\Models\OperationLog\OperationLog::create(['type' => $type, 'describe' => $describe, 'data_id' => $data_id,'detailed' => json_encode($data),'created_at' =>time()]);
        return $result;
    }
}
