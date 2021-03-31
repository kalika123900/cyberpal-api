<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'name','first_name','last_name', 'email', 'phone', 'industry', 
        'organisation_size','subject', 'message', 'user_id'
    ];

    // - User
    public function user () {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
