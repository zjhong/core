<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class OperationLog
{
    protected $type = 1;//后台操作日志
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $usernum = \auth()->id(); //操作人(要自己获取)
        $input = $request->all(); //操作的内容
        $path = $request->path(); //操作的路由
        $method = $request->method(); //操作的方法
        $ip = $request->ip(); //操作的IP
        //查询用户名名称
        $name = DB::table('users')->where('id',$usernum)->first();
        $data = [
            'input' => $input,
            'path' => $path,
            'method' => $method,
            'ip' => $ip,
            'usernum' => $usernum,
        ];
        if($path == 'api/auth/login'){
            $msg = $input['email'] . '登录成功';
        }else{
            if(empty($name->name)){
                return $next($request);
            }
            if(empty($input)){
                $msg = $name->name . '查询了'.$path.'路由的数据';
            }else{
                $msg = $name->name . '触发了'.$path.'路由的数据';
            }
        }
        self::writeLog($msg, $data, $usernum);
        return $next($request);
    }

    public function writeLog($msg, $data, $usernum)
    {
        $res = [
            'type' => $this->type,
            'describe' => $msg,
            'data_id' => empty($usernum) ? 0 : $usernum,
            'created_at' => time(),
            'detailed' => json_encode($data),
        ];
        $result = \App\Models\OperationLog\OperationLog::create($res);
        return $result;
    }
}
