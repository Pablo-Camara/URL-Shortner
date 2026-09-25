<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Refuse ambiguous legacy records before changing schema or choosing a destination.
        if (DB::table('shortlinks')->groupBy('shortstring_id')->havingRaw('COUNT(*) > 1')->exists()
            || DB::table('shortlink_urls')->where('is_redirect_url', true)->whereNotNull('shortlink_id')->groupBy('shortlink_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Resolve duplicate legacy aliases or current destinations before migrating. No history has been removed.');
        }
        Schema::table('users', fn (Blueprint $t) => $t->rememberToken());
        Schema::table('shortlinks', function (Blueprint $t) {
            $t->string('title', 120)->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->unsignedInteger('version')->default(1);
            $t->unique('shortstring_id');
            $t->index(['user_id', 'status_id', 'id']);
        });
        Schema::create('daily_clicks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shortlink_id')->constrained()->cascadeOnDelete();
            $t->date('day');
            $t->unsignedBigInteger('count')->default(0);
            $t->unique(['shortlink_id', 'day']);
            $t->index('day');
        });
        foreach ([1 => 'Active', 2 => 'Archived', 3 => 'Paused'] as $id => $name) {
            DB::table('shortlink_statuses')->insertOrIgnore(['id' => $id, 'name' => $name]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_clicks');
        Schema::table('shortlinks', function (Blueprint $t) {
            $t->dropUnique(['shortstring_id']);
            $t->dropIndex(['user_id', 'status_id', 'id']);
            $t->dropColumn(['title', 'expires_at', 'version']);
        });
        Schema::table('users', fn (Blueprint $t) => $t->dropRememberToken());
    }
};
