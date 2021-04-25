<?php


namespace App\Models\FieldMapping;


use App\Models\Base;

class FieldMapping extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'field_mapping';

    protected $fillable = ['device_id', 'field_from','field_to'];
}
