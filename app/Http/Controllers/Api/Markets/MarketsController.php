<?php


namespace App\Http\Controllers\Api\Markets;

use App\Http\Controllers\Api\Controller;

class MarketsController extends Controller
{

    public function list(){
        $result = [];
        $res = $this->read_static_cache();
        foreach ($res as $key => $val){
            $result[$key]['type'] = $val['type'];
            $result[$key]['name'] = $val['name'];
            $result[$key]['description'] = $val['description'];
            $result[$key]['version'] = $val['version'];
            $result[$key]['author'] = $val['author'];
            foreach ($val['widgets'] as $k => $v){
                $result[$key]['template'] = $v['template'];
                $result[$key]['thumbnail'] = $v['thumbnail'];
            }
        }
        $result = array_values($result);
        return $this->jsonResponse(200, 'success', $result);
    }
}
