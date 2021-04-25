<?php


namespace App\Models\Production;


use App\Models\Base;

class Production extends Base
{
    protected $table    = 'production';
    protected $fillable = ['type', 'name', 'value', 'created_at','remark','insert_at'];
    public $TYPE = [
        '1' => '种植',
        '2' => '用药',
        '3' => '收获',
    ];
}
