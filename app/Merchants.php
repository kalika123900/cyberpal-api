<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchants extends Model
{
    protected $table = "merchants";

    protected $fillable = [
        'isApproved', 'isDisabled', 'vendor_type',
        'business_name', 'business_address', 
        'organisation_size', 'user_id', 'category_id',
        'position', 'message', 'linked_url', 'twitter_url', 'facebook_url',
        'youtube_url', 'instagram_url', 'website_url'
    ];

    // - User Relation
    public function user () {
        return $this->hasOne(User::class, 'id' ,'user_id');
    }

    // - Category Relation
    public function category () {
        return $this->hasOne(Categories::class, 'id' ,'category_id');
    }
}
