<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */ 
    public function up()
    {
        // - This is only to tell users for all the temporary solutions to review.
        // - Assigned by admin only
        Schema::create('project_user', function (Blueprint $table) {
            $table->biginteger('project_id')->nullable()->unsigned(); 
            $table->foreign('project_id')->references('id')->on('projects');  

            $table->biginteger('merchant_id')->nullable()->unsigned(); 
            $table->foreign('merchant_id')->references('id')->on('users');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_user');
    }
}
