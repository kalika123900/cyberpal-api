<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resellers extends Model
{
    protected $table = 'resellers';

    protected $fillable = [
        'url', 'isPublished', 'name', 'profile_pic', 'cover_pic', 'description',
        'business_address', 'awards', 'location_id', 'content', 'website_url',
        'email', 'phone', 'city', 'country', 'lat', 'lng', 'partners'
    ];

    protected $casts = [
        'awards' => 'json',
        'partners' => 'json',
    ];

    // - Location
    public function location () {
        return $this->hasOne(Locations::class, 'id' ,'location_id');
    }
    
    // - Solution
    public function solutions () {
        return $this->belongsToMany(Solution::class, 'reseller_solution', 'reseller_id', 'solution_id');
    }

    // - Category 
    public function category () {
        return $this->hasOne(Categories::class, 'id' ,'category_id');
    }
}
