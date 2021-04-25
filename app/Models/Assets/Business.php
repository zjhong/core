<?php


namespace App\Models\Assets;


use App\Models\Base;

class Business extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';

    protected $fillable = ['name', 'created_at'];
}
