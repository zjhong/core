<?php


namespace App\Models\OperationLog;


use App\Models\Base;

class OperationLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'operation_log';

    protected $fillable = ['type', 'describe','data_id','created_at','detailed'];
}
