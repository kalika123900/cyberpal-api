<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DynamicSolutionsPage extends Model
{
    protected $table = "dynamic_solutions_page";

    protected $fillable = ["type", "order", "valid_from", "valid_to", "solution_id"];

    public function solution () {
        return $this->hasOne(Solution::class, 'id', 'solution_id');
    }
}
