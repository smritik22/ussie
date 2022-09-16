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
        Schema::create('area', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('language_id')->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->integer('country_id')->nullable();
            $table->bigInteger('governorate_id')->nullable();
            $table->string('area_image')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->decimal('default_range',3,2)->nullable();
            $table->string('updated_range')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('area');
    }
};
