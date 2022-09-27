<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('shortlinks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shortstring_id');
            $table->string('destination_email')->nullable();
            $table->tinyInteger('status_id')->unsigned();

            $table->timestamp('created_at')->useCurrent();
            $table->date('created_at_day')->default(DB::raw('CURRENT_DATE'))->index();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('shortstring_id')->references('id')->on('shortstrings');
            $table->foreign('status_id')->references('id')->on('shortlink_statuses');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shortlinks');
    }
};
