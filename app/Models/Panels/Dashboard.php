<?php


namespace App\Models\Panels;


use App\Models\Base;

class Dashboard extends Base
{
    protected $table    = 'dashboard';
    protected $fillable = ['configuration', 'assigned_customers', 'search_text', 'title','business_id'];
}
