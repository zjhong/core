<?php


namespace App\Models\Navigation;


use App\Models\Base;

class Navigation extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'navigation';

    protected $fillable = ['type','name','data','count'];
}
