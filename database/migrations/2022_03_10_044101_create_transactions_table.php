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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trans_no')->nullable();
            $table->bigInteger('property_id')->nullable();
            $table->bigInteger('subscription_plan_id')->nullable();
            $table->bigInteger('subscription_type')->nullable()->comment('0-Subscription_wise, 1-Property_wise');
            $table->bigInteger('agent_id')->nullable();
            $table->decimal('amount',13)->nullable();
            $table->decimal('property_price',13)->nullable();
            $table->bigInteger('area_id')->nullable();
            $table->tinyInteger('status')->nullable()->comment('0=Pending, 1=Paid');
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
        Schema::dropIfExists('transactions');
    }
};
