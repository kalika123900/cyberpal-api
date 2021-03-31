<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicSolutionsPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_solutions_page', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('order')->nullable();
            $table->string('valid_to')->nullable();
            $table->string('valid_from')->nullable();

            $table->bigInteger('solution_id')->unsigned()->index();
            $table->foreign('solution_id')->references('id')->on('solutions')->onDelete('cascade');
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
        Schema::dropIfExists('dynamic_solutions_page');
    }
}
