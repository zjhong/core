<?php

namespace App\Models\Customers;


use App\Models\Base;

class Customer extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table      = 'customer';
    protected $fillable   = ['additional_info', 'address', 'address2', 'city', 'country', 'email', 'phone', 'search_text', 'state', 'title', 'zip'];
    public    $timestamps = false;
}
