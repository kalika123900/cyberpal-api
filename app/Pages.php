<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $table = 'static_pages';

    protected $fillable = [
        'title', 'url', 'description', 
        'content', 'isPublished'
    ];
}
