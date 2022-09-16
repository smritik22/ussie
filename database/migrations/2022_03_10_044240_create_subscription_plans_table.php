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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('language_id')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->string('plan_name')->nullable();
            $table->text('plan_description')->nullable();
            $table->tinyInteger('plan_type')->nullable();
            $table->integer('no_of_plan_post')->nullable();
            $table->tinyInteger('is_free_plan')->nullable();
            $table->decimal('plan_price', 13, 3)->nullable();
            $table->integer('plan_duration_value')->nullable();
            $table->tinyInteger('plan_duration_type')->nullable();
            $table->decimal('extra_each_normal_post_price', 13, 3)->nullable();
            $table->tinyInteger('is_featured')->nullable();
            $table->integer('no_of_default_featured_post')->nullable();
            $table->integer('no_of_extra_featured_post')->nullable();
            $table->decimal('extra_each_featured_post_price', 13, 3)->nullable();
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
        Schema::dropIfExists('subscription_plans');
    }
};
