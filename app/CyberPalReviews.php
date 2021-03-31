<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CyberPalReviews extends Model
{
    protected $table = 'cyberpal_reviews';

    protected $fillable = [
        'rating', 'review'
    ];

    public function solution () {
        return $this->belongsTo(Solution::class, 'cyberpal_review_id' ,'id');
    }
}
