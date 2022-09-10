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
        Schema::create('shortlink_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shortlink_id')->nullable();
            $table->boolean('is_redirect_url')->index();
            $table->string('url', 2048);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('shortlink_id')->references('id')->on('shortlinks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shortlink_urls');
    }
};
