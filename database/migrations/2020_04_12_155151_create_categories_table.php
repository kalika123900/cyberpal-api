<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->text('description')->nullable();
            $table->text('icon')->nullable();
            $table->boolean('is_in_events')->default(1);
            $table->boolean('is_in_certifications')->default(1);
            $table->boolean('is_in_solutions')->default(1);
            $table->boolean('is_in_experts')->default(1);
            $table->boolean('is_in_community')->default(1);
            // - Trending 
            $table->boolean('is_in_top_searches')->default(0);

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
        Schema::dropIfExists('categories');
    }
}
