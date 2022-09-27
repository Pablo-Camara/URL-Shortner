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
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->boolean('default')->default(0)->index();
            $table->boolean('guests_permission_group')->default(0)->index();

            $table->boolean('send_shortlink_by_email_when_generating')->default(0);
            $table->boolean('edit_shortlinks_destination_url')->default(0);
            $table->boolean('view_shortlinks_total_views')->default(0);
            $table->boolean('view_shortlinks_total_unique_views')->default(0);

            $table->boolean('create_custom_shortlinks')->default(0);

            $table->unsignedInteger('max_shortlinks_with_5_or_more_of_length')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_day_with_5_or_more_of_length')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_month_with_5_or_more_of_length')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_year_with_5_or_more_of_length')->nullable()->default(null);

            $table->unsignedInteger('create_shortlinks_with_length_1')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_with_length_1')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_per_day_with_length_1')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_month_with_length_1')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_year_with_length_1')->nullable()->default(null);

            $table->unsignedInteger('create_shortlinks_with_length_2')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_with_length_2')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_per_day_with_length_2')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_month_with_length_2')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_year_with_length_2')->nullable()->default(null);

            $table->unsignedInteger('create_shortlinks_with_length_3')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_with_length_3')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_per_day_with_length_3')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_month_with_length_3')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_year_with_length_3')->nullable()->default(null);

            $table->unsignedInteger('create_shortlinks_with_length_4')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_with_length_4')->nullable()->default(0);
            $table->unsignedInteger('max_shortlinks_per_day_with_length_4')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_month_with_length_4')->nullable()->default(null);
            $table->unsignedInteger('max_shortlinks_per_year_with_length_4')->nullable()->default(null);

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
        Schema::dropIfExists('user_permissions');
    }
};
