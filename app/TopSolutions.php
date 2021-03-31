<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopSolutions extends Model
{
    protected $table = 'top_solutions';
    
    protected $fillable = ['solution_id','name', 'description', 'video_url', 'up_votes', 'down_votes', 'affiliate_link', 'website_url', 'cover_image'];

    public function solution () {
        return $this->hasOne(Solution::class, 'id', 'solution_id');
    }
}
