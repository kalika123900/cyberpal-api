<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_questions', function (Blueprint $table) {
            $table->id();
            $table->string('isApproved')->default(0);
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('total_upvotes')->default(0);

            $table->biginteger('user_id')->nullable()->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users');  

            $table->biginteger('category_id')->nullable()->unsigned(); 
            $table->foreign('category_id')->references('id')->on('categories');  

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
        Schema::dropIfExists('community_questions');
    }
}
