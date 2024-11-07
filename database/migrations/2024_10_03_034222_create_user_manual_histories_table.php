<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_manual_histories', function (Blueprint $table) {
            $table->id('user_manual_history_id');
            $table->unsignedBigInteger('user_manual_id');
            $table->string('title', 255)->nullable(false);
            $table->longText('img')->nullable(false);
            $table->string('short_desc', 200)->nullable(false);
            $table->string('initial_editor', 255)->nullable(false);
            $table->string('latest_editor', 255)->nullable(false);
            $table->string('version', 255)->nullable(false);
            $table->longText('content')->nullable(false);
            $table->enum('category', ['internal', 'eksternal'])->nullable(false);
            $table->string('size', 255)->nullable(false);

            $table->foreign('user_manual_id')->references('user_manual_id')->on('user_manuals')->onDelete('cascade');
                
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
        Schema::dropIfExists('user_manual_histories');
    }
};
