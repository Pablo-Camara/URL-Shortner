<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_user_id')->index();
            $table->string('nickname')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('avatar')->nullable();
            $table->string('avatar_original')->nullable();
            $table->text('user_token');
            $table->text('user_token_secret')->nullable();
            $table->boolean('user_protected')->nullable();
            $table->string('user_followers_count')->nullable();
            $table->string('user_friends_count')->nullable();
            $table->string('user_listed_count')->nullable();
            $table->string('user_created_at')->nullable();
            $table->string('user_favourites_count')->nullable();
            $table->string('user_utc_offset')->nullable();
            $table->string('user_time_zone')->nullable();
            $table->boolean('user_geo_enabled')->nullable();
            $table->boolean('user_verified')->nullable();
            $table->string('user_statuses_count')->nullable();
            $table->string('user_lang')->nullable();
            $table->boolean('user_contributors_enabled')->nullable();
            $table->boolean('user_is_translator')->nullable();
            $table->boolean('user_is_translation_enabled')->nullable();
            $table->boolean('user_profile_use_background_image')->nullable();
            $table->boolean('user_has_extended_profile')->nullable();
            $table->boolean('user_default_profile')->nullable();
            $table->boolean('user_default_profile_image')->nullable();
            $table->boolean('user_suspended')->nullable();
            $table->boolean('user_needs_phone_verification')->nullable();
            $table->string('user_url')->nullable();
            $table->string('user_location')->nullable();
            $table->text('user_description')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_accounts');
    }
};
