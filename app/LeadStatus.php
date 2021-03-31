<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{ 
    protected $table = 'leads_progress_logs';
    
    protected $fillable = ['lead_id','message', 'user_id','status'];

     
    // - User
    public function lead () {
        return $this->hasOne(Leads::class, 'id' ,'lead_id');
    }
  }
