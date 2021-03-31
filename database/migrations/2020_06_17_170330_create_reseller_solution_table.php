<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerSolutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_solution', function (Blueprint $table) {
            $table->bigInteger('reseller_id')->unsigned()->index();
            $table->foreign('reseller_id')->references('id')->on('resellers')->onDelete('cascade');

            $table->bigInteger('solution_id')->unsigned()->index();
            $table->foreign('solution_id')->references('id')->on('solutions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reseller_solution');
    }
}
