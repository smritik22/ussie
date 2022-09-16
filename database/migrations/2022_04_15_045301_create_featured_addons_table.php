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
        Schema::create('featured_addons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('no_of_extra_featured_post');
            $table->decimal('extra_each_featured_post_price', 13,2);
            $table->tinyInteger('status')->default(1);
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
        if (Schema::hasTable('featured_addons')) {
            Schema::dropIfExists('featured_addons');
        }
    }
};
