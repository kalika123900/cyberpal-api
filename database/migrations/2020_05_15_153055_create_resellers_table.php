<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resellers', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->boolean('isPublished')->default(0);
            $table->string('name')->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('cover_pic')->nullable();
            $table->text('description')->nullable();
            
            $table->text('website_url')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            // $table->string('organisation_size')->nullable();

            $table->string('business_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->json('awards')->nullable();

            $table->text('content')->nullable();
            $table->json('partners')->nullable();

            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            
            // - Nearest Location
            $table->biginteger('location_id')->nullable()->unsigned(); 
            $table->foreign('location_id')->references('id')->on('locations');  

            // $table->biginteger('solution_id')->nullable()->unsigned(); 
            // $table->foreign('solution_id')->references('id')->on('solutions');  

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
        Schema::dropIfExists('resellers');
    }
}
