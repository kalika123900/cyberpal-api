<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('fromWhere')->default('others');
            $table->json('requestNeeded')->nullable();
            $table->text('message')->nullable();
            $table->json('requestedResellers')->nullable();

            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('organisation_name')->nullable();
            $table->string('organisation_url')->nullable();
            $table->string('organisational_role')->nullable();
            $table->string('organisation_size')->nullable();
            $table->string('industry')->nullable();
            $table->string('budget')->nullable();
            $table->string('implementation_time_period')->nullable();
            $table->boolean('primary_sell_solution')->default(0);
            $table->boolean('marketing_services')->default(0);
            
            $table->biginteger('user_id')->nullable()->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users');  
            
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
        Schema::dropIfExists('leads');
    }
}
