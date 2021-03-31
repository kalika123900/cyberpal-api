<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposal', function (Blueprint $table) {
            $table->id();
            $table->string('isAccepted')->default(0);
            $table->string('isDeclined')->default(0);

            $table->longText('cover')->nullable();
            $table->string('proposed_price')->nullable();
            $table->string('proposed_timeline')->nullable();
            $table->json('attachments')->nullable();
            
            $table->biginteger('project_id')->nullable()->unsigned(); 
            $table->foreign('project_id')->references('id')->on('projects');  

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
        Schema::dropIfExists('proposal');
    }
}
