<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->boolean('isApproved')->default(false);
            $table->boolean('isFeatured')->default(0);
            $table->string('url')->unqiue();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('organisation_size')->default('small');
            $table->text('content')->nullable();
            $table->json('features')->nullable();
            $table->json('ease_of_operations')->nullable();
            $table->json('user_feedbacks')->nullable();
            $table->json('typical_customers')->nullable();
            $table->json('customer_support')->nullable();
            $table->json('cons')->nullable();
            $table->json('pros')->nullable();
            $table->json('pricing')->nullable();
            $table->json('compatibility')->nullable();
            $table->json('deployments')->nullable();
            $table->json('languages')->nullable();
            $table->json('market')->nullable();
            $table->string('contact_link')->nullable();
            $table->string('up_votes')->default(0);
            $table->string('down_votes')->default(0);
            $table->string('total_votes')->default(0);

            $table->string('order')->nullable();
            
            // - Relations
            $table->biginteger('vendor_id')->nullable()->unsigned(); 
            $table->foreign('vendor_id')->references('id')->on('merchants');  

            $table->biginteger('cyberpal_review_id')->nullable()->unsigned(); 
            $table->foreign('cyberpal_review_id')->references('id')->on('cyberpal_reviews');  
            
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
        Schema::dropIfExists('solutions');
    }
}
