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
        Schema::create('webmaster_settings', function (Blueprint $table) {
            $table->id();
            
            $table->string('mail_driver')->nullable();
            $table->string('mail_host')->nullable();
            $table->string('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_no_replay')->nullable();
            $table->string('mail_title')->nullable();
            $table->longText('mail_template')->nullable();
            $table->tinyInteger('nocaptcha_status')->nullable();
            $table->string('nocaptcha_secret')->nullable();
            $table->string('nocaptcha_sitekey')->nullable();
            $table->tinyInteger('google_tags_status')->nullable();
            $table->string('google_tags_id')->nullable();
            $table->text('google_analytics_code')->nullable();

            $table->tinyInteger('login_facebook_status')->nullable();
            $table->string('login_facebook_client_id')->nullable();
            $table->string('login_facebook_client_secret')->nullable();

            $table->tinyInteger('login_twitter_status')->nullable();
            $table->string('login_twitter_client_id')->nullable();
            $table->string('login_twitter_client_secret')->nullable();

            $table->tinyInteger('login_google_status')->nullable();
            $table->string('login_google_client_id')->nullable();
            $table->string('login_google_client_secret')->nullable();

            $table->tinyInteger('login_linkedin_status')->nullable();
            $table->string('login_linkedin_client_id')->nullable();
            $table->string('login_linkedin_client_secret')->nullable();

            $table->tinyInteger('dashboard_link_status')->nullable();
            $table->string('timezone')->nullable();
            $table->string('version',20)->nullable();


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
        if (Schema::hasTable('webmaster_settings')) {
            Schema::drop('webmaster_settings');
        }
    }
};
