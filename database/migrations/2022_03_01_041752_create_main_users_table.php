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
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->bigInteger('mobile_number')->nullable();
            $table->string('password')->nullable();
            $table->integer('otp')->nullable();
            $table->tinyInteger('is_otp_varified')->nullable()->default(0);
            $table->timestamp('otp_expire_time')->nullable();
            $table->tinyInteger('user_type')->nullable()->default(1);
            $table->tinyInteger('agent_type')->nullable()->default(1);
            $table->timestamp('agent_joined_date')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('remember_token')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        if (Schema::hasTable('users')) {
            Schema::drop('users');
        }
    }
};
