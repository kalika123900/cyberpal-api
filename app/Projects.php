<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $table="projects";

    protected $fillable=[
        'service_type', 'name', 'email', 'phone', 'current_service_provider','estimated_setup_date',
        'business_name', 'organisation_size', 'budget', 'message', 'reference_id', 'status', 'service_type',
        'project_type', 'job_type', 'job_location', 'skills_required', 'min_experience', 'language_preference', 
        'expertise', 'project_timeline', 'message', 'category_id', 'user_id', 'merchant_id', 'website',
        'client_status', 'merchant_status'
    ];

    protected $casts = [
        'skills_required' => 'json',
        'language_preference' => 'json',
    ];

    // - Category Relation
    public function category () {
        return $this->hasOne(Categories::class, 'id' ,'category_id');
    }

    // - User Relation
    public function user () {
        return $this->hasOne(User::class ,'id', 'user_id');
    }

    // - Merchant Relation
    public function merchant () {
        return $this->hasOne(Merchants::class, 'id', 'merchant_id');
    }

    // - Invite Merchant Relation
    public function invitedMerchants () {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'merchant_id');
    }
    
    // - Proposals
    public function proposals () {
        return $this->hasMany(Proposals::class, 'project_id', 'id');
    }
 
    // - Merchant Submitted Proposals
    public function merchantProposals () {
        return $this->hasOne(Proposals::class, 'project_id', 'id');
    }
}
