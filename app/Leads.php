<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{ 
    protected $table = 'leads';
    
    protected $fillable = [
        'fromWhere','solution_id', 'requestNeeded','message', 'requestedResellers', 'full_name', 'phone', 'email',
        'organisation_name', 'organisation_url', 'organisational_role', 'organisation_size', 'industry',
        'budget_from', 'budget_to', 'implementation_time_period', 'open_emerging_vendors', 'requirement_type' , 'include_resellers',
        'reseller_type', 'user_id', 'location_id', 'current_solution', 'solution_type', 'merchant_id', 'merchant_lead_status',
        'merchant_name', 'status', 'budget'
    ];

     
    protected $casts = [
        'requestedResellers' => 'json',
        'solution_id'   => 'json'
    ];
 
    // - User
    public function user () {
        return $this->hasOne(User::class, 'id' ,'user_id');
    }
         
    // - Location
    public function location () {
        return $this->hasOne(Locations::class, 'id' ,'location_id');
    }

    // - Merchant
    public function merchant () {
        return $this->hasOne(User::class, 'id' , 'merchant_id');
    }
    public function solutions () {
        return $this->hasOne(Solution::class, 'id' , 'solution_id')->select('title','id','url');
    }
    public function lead_status () {
        return $this->hasMany(LeadStatus::class, 'lead_id' , 'id');
    }
}
