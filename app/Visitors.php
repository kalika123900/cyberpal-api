<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visitors extends Model
{
    protected $table = 'visitors';

    protected $fillable = [            
       'page_type','page_id','ip_address','page_title'
    ];
}
