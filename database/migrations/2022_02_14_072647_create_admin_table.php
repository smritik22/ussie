<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_admin', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->nullable();
            $table->string('email',255)->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('photo')->nullable();
            $table->string('user_type')->nullable();
            $table->tinyInteger('status')->nullable();
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
        if (Schema::hasTable('user_admin')) {
            Schema::dropIfExists('user_admin');
        }
    }
}
