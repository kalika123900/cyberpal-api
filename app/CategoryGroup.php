<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryGroup extends Model
{
    protected $table = 'category_group';

    protected $fillable = [
        'title', 'categories'
    ];

      protected $casts = [
          'categories' => 'json'
      ];
}
