<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityQuestions extends Model
{
    protected $table = 'community_questions';

    protected $fillable = ['isApproved', 'url', 'title', 'description', 'total_upvotes', 'user_id', 'category_id'];

    public function user () {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function answers () {
        return $this->hasMany(CommunityAnswers::class, 'question_id', 'id');
    }

    public function category () {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }
}
