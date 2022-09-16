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
        Schema::create('amenity', function (Blueprint $table) {
            $table->id();
            $table->integer('language_id')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->string('amenity_name');
            $table->tinyInteger('status')->nullable()->default(1);
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
        if (Schema::hasTable('amenity')) {
            Schema::drop('amenity');
        }
    }
};
