<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->migration()->down();
    }

    private function migration(): object
    {
        return require database_path('migrations/2026_09_25_000001_modernize_link_management.php');
    }

    private function legacyLink(): int
    {
        $owner = DB::table('users')->insertGetId(['name' => 'Legacy', 'email' => 'legacy@example.test', 'guest' => false]);
        $alias = DB::table('shortstrings')->insertGetId(['shortstring' => 'OldLink', 'is_available' => false, 'is_custom' => true, 'length' => 7]);
        $link = DB::table('shortlinks')->insertGetId(['user_id' => $owner, 'shortstring_id' => $alias, 'status_id' => 1]);
        DB::table('shortlink_urls')->insert([
            ['shortlink_id' => $link, 'url' => 'https://example.com/old', 'is_redirect_url' => false],
            ['shortlink_id' => $link, 'url' => 'https://example.com/current', 'is_redirect_url' => true],
        ]);

        return $link;
    }

    public function test_upgrade_preserves_legacy_aliases_owners_and_destination_history(): void
    {
        $link = $this->legacyLink();
        $this->migration()->up();
        $this->assertDatabaseHas('shortlinks', ['id' => $link, 'version' => 1]);
        $this->assertDatabaseCount('shortlink_urls', 2);
        $this->get('/OldLink')->assertRedirect('https://example.com/current');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_ambiguous_legacy_destinations_fail_before_any_schema_changes(): void
    {
        $link = $this->legacyLink();
        DB::table('shortlink_urls')->insert(['shortlink_id' => $link, 'url' => 'https://example.org/conflict', 'is_redirect_url' => true]);
        try {
            $this->migration()->up();
            $this->fail('Ambiguous legacy data must require an explicit resolution.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('duplicate legacy', $exception->getMessage());
        }
        $this->assertFalse(Schema::hasColumn('shortlinks', 'version'));
        $this->assertFalse(Schema::hasColumn('users', 'remember_token'));
        $this->assertDatabaseCount('shortlink_urls', 3);
    }
}
