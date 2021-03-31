<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    
    protected $fillable = [
        'isPublished', 'title', 'description', 'vendor', 'vendor_link', 'published_date',
        'price', 'affiliate_link', 'language', 'whats_included', 'expertize_level', 'total_views', 'total_watch_hours',
        'is_certification_provided', 'total_views', 'url', 'content', 'image', 'category_id', 'total_videos'
    ];

    // - Category
    public function category () {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }
}
