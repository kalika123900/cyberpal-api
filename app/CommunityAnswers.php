<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityAnswers extends Model
{
    protected $table = 'community_answers';

    protected $fillable = ['isAccepted', 'description', 'user_id', 'question_id'];

    public function user () {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    
    public function question () {
        return $this->hasOne(CommunityQuestions::class, 'id', 'question_id');
    }
};