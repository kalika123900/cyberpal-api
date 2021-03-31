<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->boolean('isPublished')->default(false);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->longText('content')->nullable();

            $table->string('vendor')->nullable();
            $table->string('vendor_link')->nullable();
            
            $table->string('published_date')->nullable();
            $table->string('price')->nullable();
            $table->string('affiliate_link')->nullable();
            $table->string('language')->nullable();
            $table->longText('whats_included')->nullable();

            $table->string('expertize_level')->nullable();
            
            $table->string('total_views')->nullable();
            $table->string('total_watch_hours')->nullable();
            $table->string('is_certification_provided')->default(false);
            $table->string('total_videos')->nullable();
            $table->text('image')->nullable();

            $table->text('full_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->biginteger('location_id')->nullable()->unsigned(); 
            $table->foreign('location_id')->references('id')->on('locations');  

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
        Schema::dropIfExists('courses');
    }
}
