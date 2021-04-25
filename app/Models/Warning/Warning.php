<?php


namespace App\Models\Warning;


use App\Models\Base;

class Warning extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'warning_log';

    protected $fillable = ['type', 'describe','data_id','created_at'];

    /**
     * @param string $type
     * @param string $describe
     * @param string $data_id
     */
    public static function insertWarning($type,$describe,$data_id)
    {
        if(empty($type) || empty($describe) || empty($data_id)){
            return json_encode(['data' => []]);
        }
        $result = Warning::create(['type' => $type, 'describe' => $describe, 'data_id' => $data_id, 'created_at' =>time()]);
        return $result;
    }
}
