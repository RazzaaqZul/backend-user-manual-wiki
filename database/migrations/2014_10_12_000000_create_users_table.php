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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username', 255)->unique("users_username_unique")->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('email', 255)->unique("users_email_unique")->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->enum("role", ['admin', 'technical_writer', 'user'])->default('technical_writer')->nullable(false);
            $table->string("token", 100)->nullable()->unique("users_token_unique");
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
};
