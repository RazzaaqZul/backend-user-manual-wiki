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
        Schema::create('user_manuals', function (Blueprint $table) {
            $table->id('user_manual_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title', 255)->nullable(false)->unique("users_manuals_title_unique");
            $table->longText('img')->nullable(false);
            $table->string('short_desc', 200)->nullable(false);
            $table->string('initial_editor', 255)->nullable(false);
            $table->string('latest_editor', 255)->nullable(false);
            $table->string('version', 255)->nullable(false);
            $table->string('update_desc', 200)->nullable();
            $table->longText('content')->nullable(false);
            $table->enum('category', ['internal', 'eksternal'])->nullable(false);
            $table->string('size', 255)->nullable(false);
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('user_manuals');
    }
};
