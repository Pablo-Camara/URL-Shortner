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
        Schema::create('github_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('github_user_id');
            $table->string('nickname')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('avatar')->nullable();
            $table->string('user_token');
            $table->string('user_refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('approved_scopes');
            $table->string('user_url')->nullable();
            $table->string('user_type')->nullable();
            $table->boolean('user_is_site_admin')->nullable();
            $table->string('user_company')->nullable();
            $table->string('user_blog_link')->nullable();
            $table->string('user_location')->nullable();
            $table->string('user_hireable')->nullable();
            $table->string('user_bio')->nullable();
            $table->string('user_twitter_username')->nullable();
            $table->integer('user_total_public_repos')->nullable();
            $table->integer('user_total_followers')->nullable();
            $table->string('user_acc_created_at')->nullable();

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
        Schema::dropIfExists('github_accounts');
    }
};
