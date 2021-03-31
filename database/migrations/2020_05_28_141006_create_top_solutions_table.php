<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopSolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_solutions', function (Blueprint $table) {
            $table->id();
            $table->string('cover_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('up_votes')->default(0);
            $table->string('down_votes')->default(0);
            $table->string('total_votes')->default(0);
            $table->string('affiliate_link')->default(0);
            $table->string('order')->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->string('website_url')->nullable();
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
        Schema::dropIfExists('top_solutions');
    }
}
