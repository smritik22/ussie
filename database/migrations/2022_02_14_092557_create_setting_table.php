<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('site_title_ar')->nullable();
            $table->string('site_title_en')->nullable();
            $table->string('site_desc_ar')->nullable();
            $table->string('site_desc_en')->nullable();
            $table->text('site_keywords_ar')->nullable();
            $table->text('site_keywords_en')->nullable();
            $table->string('site_webmails')->nullable();
            $table->string('site_url')->nullable();
            $table->tinyInteger('site_status');
            $table->text('close_msg')->nullable();

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
        if (Schema::hasTable('setting')) {
            Schema::dropIfExists('setting');
        }
    }
}
