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
        Schema::create('google_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('google_user_id')->index();
            $table->string('nickname')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('avatar')->nullable();
            $table->string('avatar_original')->nullable();
            $table->string('user_picture')->nullable();
            $table->boolean('user_email_verified')->nullable();
            $table->string('user_locale')->nullable();
            $table->boolean('user_verified_email')->nullable();
            $table->text('user_token');
            $table->text('user_refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('approved_scopes');
            $table->string('user_link')->nullable();

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
        Schema::dropIfExists('google_accounts');
    }
};
