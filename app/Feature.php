<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model{
    protected $table = 'feature_master';

    protected $fillable = [
        'feature_name', 'type', 'category'   
    ];

    public $timestamps = true;

     public function categoryDetail () {
        return $this->hasOne(Categories::class, 'id' ,'catetory');
    }
}