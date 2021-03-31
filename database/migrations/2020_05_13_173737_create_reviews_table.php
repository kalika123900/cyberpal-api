<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->boolean('isApproved')->default(0);
            $table->string('solution_type')->default('vendor');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('business_name')->nullable();
            $table->string('business_url')->nullable();
            $table->string('organisation_size')->nullable();
            $table->string('message')->nullable();
            $table->string('isReseller')->default("false");
            $table->string('user_designation')->nullable();
            $table->string('user_designation_type')->nullable();
            $table->string('user_involvement')->nullable();

            $table->string('rating')->default(0);
            $table->string('review')->nullable();
            $table->string('review_pros')->nullable();
            $table->string('review_cons')->nullable();

            $table->string('support_rating')->default(0);
            $table->string('support_review')->nullable();

            $table->json('primary_purpose')->nullable();
            $table->string('currently_using')->default(0);
            $table->string('ease_of_admin')->default(0);
            $table->string('ease_of_doing_business')->default(0);
            $table->string('meet_requirements')->default(0);
            $table->string('ease_of_use')->default(0);
            $table->string('ease_of_setup')->default(0);
            $table->text('about_user')->nullable();

            $table->biginteger('user_id')->nullable()->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users');  
            
            $table->biginteger('solution_id')->nullable()->unsigned(); 
            $table->foreign('solution_id')->references('id')->on('solutions');  

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
        Schema::dropIfExists('reviews');
    }
}
