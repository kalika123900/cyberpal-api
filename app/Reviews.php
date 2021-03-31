<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $table = "reviews";

    protected $fillable = [
        'isApproved', 'rating', 'review', 'user_id', 'business_name', 'business_url',
        'solution_id', 'solution_type', 'name', 'email', 'phone', 'organisation_size',
        'message', 'isReseller', 'user_designation', 'user_designation_type', 'user_involvement',
        'rating', 'review', 'review_pros', 'review_cons', 'support_rating', 'support_review',
        'primary_purpose', 'currently_using', 'ease_of_admin', 'ease_of_doing_business', 
        'meet_requirements', 'ease_of_use', 'ease_of_setup', 'about_user', "cons", "pros", 
        "job_type"

    ];

    // - User
    public function user () {
        return $this->hasOne(User::class, 'id' ,'user_id');
    }
        
    // - Solution
    public function solution () {
        return $this->hasOne(Solution::class, 'id' ,'solution_id');
    }
}
