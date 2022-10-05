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
        Schema::create('shortstrings', function (Blueprint $table) {
            $table->id();
            $table->string('shortstring', 255)->unique();
            $table->boolean('is_available')->default(1)->index();
            $table->boolean('is_custom')->default(0)->index();
            $table->unsignedTinyInteger('length')->index();
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
        Schema::dropIfExists('shortstrings');
    }
};
