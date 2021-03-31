<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proposals extends Model
{
    //
    protected $table = 'proposal';

    protected $fillable = [
        'isAccepted', 'isDeclined', 'cover', 'proposed_price', 'proposed_timeline',
        'project_id', 'merchant_id', 'attachment'
    ];

    public function project () {
        return $this->hasOne(Projects::class, 'id', 'project_id');
    }

    public function merchant () {
        return $this->hasOne(User::class, 'id', 'merchant_id');
    }
}
