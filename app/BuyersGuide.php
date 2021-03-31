<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyersGuide extends Model
{
    protected $table = 'buyers_guide';
    
    protected $fillable = ['isPublished', 'title', 'url', 'content', 'image'];
}
