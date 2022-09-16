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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('agent_id')->nullable();
            $table->string('property_id')->nullable();
            $table->string('property_name')->nullable();
            $table->text('property_description')->nullable();
            $table->tinyInteger('property_for')->nullable();
            $table->integer('property_type')->nullable();
            $table->bigInteger('area_id')->nullable();
            $table->text('property_address')->nullable();
            $table->string('property_address_latitude')->nullable();
            $table->string('property_address_longitude')->nullable();
            $table->text('property_amenities_ids')->nullable();
            $table->string('bedroom_type')->nullable();
            $table->bigInteger('total_bedrooms')->nullable();
            $table->string('bathroom_type')->nullable();
            $table->bigInteger('total_bathrooms')->nullable();
            $table->bigInteger('total_toilets')->nullable();
            $table->decimal('property_sqft_area')->nullable();
            $table->decimal('base_price')->nullable();
            $table->bigInteger('condition_type_id')->nullable();
            $table->bigInteger('completion_status_id')->nullable();
            $table->timestamp('property_subscription_enddate')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('properties')) {
            Schema::drop('properties');
        }
    }
};
