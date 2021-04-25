<?php


namespace App\Models\Resources;


use App\Models\Base;
use Illuminate\Support\Facades\DB;

class Resources extends Base
{
    protected $table = 'resources';
    protected $fillable = ['cpu', 'mem', 'created_at'];

    static public function getNewResource($field){
        return DB::select('select created_at,'.$field.' from ( select * from resources order by created_at desc limit 10) as c order by created_at asc');
    }
}
