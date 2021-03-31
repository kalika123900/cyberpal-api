<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    
    protected $fillable = [
        'isPublished', 'title', 'url', 'content', 'image', 'description', 'provided_by',
        'provided_by_link', 'start_date', 'end_date', 'entry_fee', 'full_address', 'total_views',
        'refund_policy', 'start_time', 'end_time', 'affiliate_link', 'category_id', 'managed_by',
        'managed_by_link'
    ];

    // - Category
    public function category () {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }  
}
