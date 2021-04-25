<?php


namespace App\Http\Controllers\Api\Dictionary;


use App\Http\Controllers\Api\Controller;
use App\Models\Dictionary\Dictionary;

class DictionaryController extends Controller
{
    public function index(){
        $post      = request()->all();
        $Dictionary = new Dictionary();
        $limit     = 30;
        if (isset($post['name'])) {
            $Dictionary = $Dictionary->where('name',$post['name']);
        }
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        $Dictionary = $Dictionary->paginate($limit);
        return $this->jsonResponse(200, 'success', $Dictionary);
    }
}
