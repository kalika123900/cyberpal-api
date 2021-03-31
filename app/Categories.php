<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name', 'url', 'description', 'icon', 'category_details', 
        'is_in_events', 'is_in_solutions', 'is_in_certifications',
        'is_in_community', 'is_in_experts', 'is_in_top_searches'
    ];

      protected $casts = [
          'category_details' => 'json'
      ];

    // - Solution Relation
    public function solutions () {
        return $this->hasMany(Solution::class, 'category_id' ,'id');
    }

    // - Events Relation
    public function events () {
        return $this->hasMany(Event::class, 'category_id' ,'id');
    }
}
