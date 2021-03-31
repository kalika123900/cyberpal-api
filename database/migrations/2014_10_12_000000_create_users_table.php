<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('user_type')->nullable();
            $table->string('profile_picture')->default('customer-default.png');
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('isVerified')->default(0);
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            $table->string('organisation_name')->nullable();
            $table->string('organisation_url')->nullable();
            $table->string('organisational_role')->nullable();
            $table->string('organisation_size')->nullable();
            $table->string('industry')->nullable();

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
        Schema::dropIfExists('users');
    }
}
