<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('isPublished')->default(0);
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('provided_by')->nullable();
            $table->string('provided_by_link')->nullable();
            $table->string('managed_by')->nullable();
            $table->string('managed_by_link')->nullable();
            $table->string('start_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_date')->nullable();
            $table->string('end_time')->nullable();
            $table->string('entry_fee')->nullable();
            $table->string('full_address')->nullable();
            $table->string('city')->nullable();
            $table->string('image')->nullable()->default('placeholder.png');
            $table->string('total_views')->default(0);
            $table->longText('refund_policy')->nullable();
            $table->string('affiliate_link')->nullable();
            $table->biginteger('category_id')->nullable()->unsigned(); 
            $table->foreign('category_id')->references('id')->on('categories');  

            $table->string('type')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->biginteger('location_id')->nullable()->unsigned(); 
            $table->foreign('location_id')->references('id')->on('locations');  

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
        Schema::dropIfExists('events');
    }
}
