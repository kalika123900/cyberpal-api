<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique();
            $table->string('status')->default('processing');
            $table->string('service_type')->nullable();
            $table->string('current_service_provider')->nullable();
            $table->string('estimated_setup_date')->nullable();
            $table->string('business_name')->nullable();
            $table->string('budget')->nullable();
            $table->string('organisation_size')->nullable();
            $table->string('project_type')->default('one-time');
            $table->string('job_type')->nullable();
            $table->string('job_location')->nullable();
            $table->json('skills_required')->nullable();
            $table->string('min_experience')->nullable();
            $table->json('language_preference')->nullable();
            $table->string('expertise')->nullable();
            $table->string('project_timeline')->nullable();

            $table->string('website')->nullable();

            $table->text('message')->nullable();

            $table->biginteger('category_id')->nullable()->unsigned(); 
            $table->foreign('category_id')->references('id')->on('categories');  

            $table->biginteger('user_id')->nullable()->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users');  

            $table->biginteger('merchant_id')->nullable()->unsigned(); 
            $table->foreign('merchant_id')->references('id')->on('users');  

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
        Schema::dropIfExists('projects');
    }
}
