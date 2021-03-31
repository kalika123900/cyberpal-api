<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'uid', 'email', 'phone', 'user_type', 'profile_picture', 'isVerified', 'password', 
        'email_verified_at', 'provider', 'provider_id', 'isActive', 'activation_token', 'profile_picture',
        'organisation_name', 'organisation_url', 'organisational_role', 'organisation_size', 'industry', 'skills'
    ];

    protected $hidden = [
        'password', 'remember_token', 'activation_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'skills' => 'json',
    ];

    public function merchant () {
        return $this->belongsTo(Merchants::class, 'id', 'user_id');
    }

    // public function getTotalProjectsCountAttribute () {
    //     return $this->belongsToMany(Projects::class, 'project_user', 'merchant_id', 'project_id')->count();
    // }

    // public function getCompletedProjectsCountAttribute () {
    //     return $this->belongsToMany(Projects::class, 'project_user', 'merchant_id', 'project_id')->where('status', 'completed')->count();
    // }

    public function merchantInvites () {
        return $this->belongsToMany(Projects::class, 'project_user', 'merchant_id', 'project_id');
    }

    public function merchantLeads () {
        return $this->hasMany(Leads::class, 'merchant_id', 'id');
    }
    
    public function getLeadsCountAttribute ()
    {
        return $this->merchantLeads->count();
    }

    public function getCompletedLeadsCountAttribute ()
    {
        return $this->merchantLeads->where('status', 'completed')->count();
    }

    public function getActiveLeadsCountAttribute ()
    {
        return $this->merchantLeads->where('status', 'active')->count();
    }

    public function getReviewingLeadsCountAttribute ()
    {
        return $this->merchantLeads->where('status', 'reviewing')->count();
    }

    public function reviews () {
        return $this->hasMany(Reviews::class, 'user_id', 'id');
    }
}
