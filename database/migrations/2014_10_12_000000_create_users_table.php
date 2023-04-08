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
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('image')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('role')->default("user");
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->integer("manager_id")->nullable();
            $table->integer("weight")->nullable();
            $table->integer("height")->nullable();
            $table->string("job")->nullable();
            $table->boolean('kvkk_verified')->default(false);
            $table->boolean('is_online_diet')->default(null);
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
