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
        Schema::create('property_conditions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('language_id')->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->string('condition_text')->nullable();
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
        if (Schema::hasTable('property_conditions')) {
            Schema::drop('property_conditions');
        }
    }
};
