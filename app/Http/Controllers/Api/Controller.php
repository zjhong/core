<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class Controller extends BaseController
{

    public function __construct(Request $request)
    {
        //set temp trace code
        config(['TRACE_CODE' => strtoupper(uniqid("", false))]);

        Log::info('API(' . config('TRACE_CODE') . '): ' . request()->fullUrl() . chr(10) . 'Request:', $request->all());
    }

    /**
     * @param $status
     * @param $code
     * @param array $data
     * @param string $message
     * @param array $errorFields
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponse($code, $message = '', $data = [], $errorFields = [])
    {
        $responseArray = [
            'code' => $code,
            'data' => $data
        ];
        if ($message) {
            $responseArray['message'] = $message;
        }

        if (!empty($errorFields)) {
            $responseArray['error_fields'] = $errorFields;
        }

        Log::info('API(' . config('TRACE_CODE') . '): ' . request()->fullUrl() . chr(10) . 'Response:', $responseArray);

        return response()->json($responseArray);
    }

    /**
     * 读结果缓存文件
     * @params string $cache_name
     * @return array  $data
     */
    function read_static_cache($cache_name = 'extensions')
    {
        $cache_file_path = dirname(public_path()) . '/storage/app/' . $cache_name . '.php';
        if (file_exists($cache_file_path)) {
            $data = include($cache_file_path);
            return $data;
        } else {
            return false;
        }
    }

    /**
     * @return string  $data
     */
    function RandStr()
    {
        $strs = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $data = substr(str_shuffle($strs), mt_rand(0, strlen($strs) - 11), 10);
        return $data;
    }

    /*
    * 二维数组按照指定字段进行排序
    * @params array $array 需要排序的数组
    * @params string $field 排序的字段
    * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
    */
    public function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }

    /*
    * 二维数组按照指定字段进行去重
    * @params array $array 需要去重的数组
    * @params string $key  去重的字段
    */
    public function array_unset_tt($arr, $key)
    {
        $res = array();
        foreach ($arr as $value) {
            if (isset($res[$value[$key]])) {
                unset($value[$key]);
            } else {
                $res[$value[$key]] = $value;
            }
        }
        return $res;
    }

    static function log($name, $title, $content, $level = Logger::INFO, $file = 'lumen')
    {
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler(storage_path
        ('logs/' . $name . '.' . $file . '.log'), 0));
        if ($level === Logger::INFO) {
            $log->info($name, ['data' => $content]);
        } elseif ($level === Logger::ERROR) {
            $log->error($name, ['data' => $content]);
        }
    }

    /**
     * 信息日志
     * @param $title
     * @param $content
     * @param string $file
     */
    static function logInfo($title, $content, $file = 'lumen')
    {
        self::log('admin', $title, $content, Logger::INFO, $file);
    }

    /**
     * 错误日志
     * @param $title
     * @param $content
     * @param string $file
     */
    static function logError($title, $content, $file = 'lumen')
    {
        self::log('admin', $title, $content, Logger::ERROR, $file);
    }

    /**
     * @description Make  call
     * @param       $url
     * @param array $params
     */
    public static function HTTPGet($url, array $params)
    {
        $query = http_build_query($params);
        $ch = curl_init($url . '?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * insert photo
     * @params string $image
     * @return array  $filename
     */
    public function applyImage($image){
        if ($image && $image->isValid()) {
            $destinationPath = storage_path('app/public/photos/');
            // 如果目标目录不存在，则创建之
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath,0777,true);
            }
            // 文件名
            $filename = date('Ymd').'/'.time() . '-' . $image->getClientOriginalName();
            // 保存文件到目标目录
            $image->move($destinationPath, $filename);
            return $filename;
        }
    }
}
