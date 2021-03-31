<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_answers', function (Blueprint $table) {
            $table->id();
                        
            $table->string('isAccepted')->default(0);
            $table->text('description')->nullable();

            $table->biginteger('user_id')->nullable()->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users');  

            $table->biginteger('question_id')->nullable()->unsigned(); 
            $table->foreign('question_id')->references('id')->on('community_questions'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('community_answers');
    }
}
