<?php


namespace App\Models\Dictionary;


use App\Models\Base;

class Dictionary extends Base
{
    /**
     * The table dictionary with the model.
     *
     * @var string
     */
    protected $table      = 'dictionary';
    protected $fillable   = ['name', 'parent_id', 'sort', 'created_at', 'code'];
}
