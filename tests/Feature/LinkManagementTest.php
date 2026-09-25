<?php

namespace Tests\Feature;

use App\Models\DailyClick;
use App\Models\Shortlink;
use App\Models\Shortstring;
use App\Models\User;
use App\Services\LinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        return [...['title' => 'Documentation', 'url' => 'https://example.com/docs', 'alias' => 'docs'], ...$extra];
    }

    private function createLink(?User $user = null, array $extra = []): Shortlink
    {
        return app(LinkService::class)->create($user ?? User::factory()->create(), $this->payload($extra));
    }

    public function test_guests_cannot_manage_links(): void
    {
        $this->getJson('/api/links')->assertUnauthorized();
        $this->postJson('/api/links', $this->payload())->assertUnauthorized();
        $this->getJson('/api/stats')->assertUnauthorized();
    }

    public function test_link_creation_is_scoped_and_only_exposes_public_account_fields(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/links', $this->payload())->assertCreated()->assertJsonPath('data.alias', 'docs')->assertJsonPath('data.status', 'active')->assertJsonPath('data.version', 1);
        $this->assertDatabaseHas('shortlinks', ['id' => $response->json('data.id'), 'user_id' => $user->id]);
        $response->assertJsonMissingPath('data.user_id')->assertJsonMissingPath('data.password');
    }

    public function test_aliases_are_unique_immutable_and_can_claim_unused_legacy_strings(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Shortstring::create(['shortstring' => 'docs', 'is_available' => true, 'is_custom' => false, 'length' => 4]);
        $link = $this->postJson('/api/links', $this->payload())->assertCreated()->json('data');
        $this->postJson('/api/links', $this->payload())->assertUnprocessable();
        $this->assertDatabaseCount('shortlinks', 1);
        $this->assertDatabaseCount('shortlink_urls', 1);
        $this->patchJson('/api/links/'.$link['id'], $this->payload(['version' => 1, 'alias' => 'changed']))->assertUnprocessable();
        $this->postJson('/api/links', $this->payload(['alias' => null]))->assertCreated();
    }

    public function test_reserved_and_malformed_aliases_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['app', 'api', 'UPPER', 'with space', 'a', '-bad', 'bad-', 'double--dash'] as $alias) {
            $this->postJson('/api/links', $this->payload(['alias' => $alias]))->assertUnprocessable();
        }
        $this->assertDatabaseCount('shortlinks', 0);
    }

    public function test_destinations_reject_script_schemes_credentials_whitespace_and_self_loops(): void
    {
        config(['app.url' => 'https://relay.example']);
        $this->actingAs(User::factory()->create());
        foreach (['javascript:alert(1)', 'data:text/html,hi', 'file:///etc/passwd', 'ftp://example.com', 'https://user:pass@example.com/', 'https://relay.example/docs', 'https://RELAY.EXAMPLE./docs', 'http://localhost/a', 'https://example.com/with space', "https://example.com/\r\nInjected: yes"] as $url) {
            $this->postJson('/api/links', $this->payload(['url' => $url]))->assertUnprocessable();
        }
        $this->assertDatabaseCount('shortlinks', 0);
    }

    public function test_another_user_cannot_read_edit_pause_or_see_analytics_for_a_link(): void
    {
        $link = $this->createLink();
        $this->actingAs(User::factory()->create());
        $this->getJson('/api/links/'.$link->id)->assertForbidden();
        $this->patchJson('/api/links/'.$link->id, ['title' => 'Stolen', 'url' => 'https://example.org', 'version' => 1])->assertForbidden();
        $this->patchJson('/api/links/'.$link->id.'/status', ['status' => 'paused', 'version' => 1])->assertForbidden();
        $this->getJson('/api/links')->assertJsonCount(0, 'data');
        DailyClick::create(['shortlink_id' => $link->id, 'day' => today()->toDateString(), 'count' => 90]);
        $this->getJson('/api/stats')->assertJsonPath('total_clicks', 0);
    }

    public function test_destination_history_and_optimistic_lock_prevent_lost_edits(): void
    {
        $user = User::factory()->create();
        $link = $this->createLink($user);
        $this->actingAs($user);
        $data = ['title' => 'Updated', 'url' => 'https://example.org/new', 'version' => 1];
        $this->patchJson('/api/links/'.$link->id, $data)->assertOk()->assertJsonPath('data.version', 2)->assertJsonCount(2, 'data.history');
        $this->patchJson('/api/links/'.$link->id, [...$data, 'url' => 'https://example.net/stale'])->assertConflict();
        $this->assertSame('https://example.org/new', $link->destination()->value('url'));
        $this->assertSame(1, $link->history()->where('is_redirect_url', true)->count());
        $this->patchJson('/api/links/'.$link->id, [...$data, 'version' => 2])->assertOk()->assertJsonCount(2, 'data.history');
    }

    public function test_editable_redirects_are_temporary_uncached_and_do_not_set_cookies(): void
    {
        $link = $this->createLink();
        $this->get('/docs')->assertStatus(302)->assertRedirect('https://example.com/docs')->assertHeader('Cache-Control', 'no-store, private')->assertHeader('Referrer-Policy', 'no-referrer')->assertHeaderMissing('Set-Cookie');
        $this->get('/docs')->assertRedirect();
        $this->assertSame(2, (int) DailyClick::sum('count'));
        $this->head('/docs')->assertRedirect();
        $this->assertSame(2, (int) DailyClick::sum('count'));
        $this->assertEqualsCanonicalizing(['id', 'shortlink_id', 'day', 'count'], array_keys(DailyClick::first()->getAttributes()));
    }

    public function test_pause_archive_restore_expiry_and_missing_destinations_behave_consistently(): void
    {
        $user = User::factory()->create();
        $link = $this->createLink($user);
        $this->actingAs($user);
        $this->patchJson('/api/links/'.$link->id.'/status', ['status' => 'paused', 'version' => 1])->assertOk();
        $this->get('/docs')->assertStatus(410);
        $this->patchJson('/api/links/'.$link->id.'/status', ['status' => 'archived', 'version' => 2])->assertOk();
        $this->get('/docs')->assertStatus(410);
        $this->getJson('/api/links')->assertJsonCount(0, 'data');
        $this->getJson('/api/links?status=archived')->assertJsonCount(1, 'data');
        $this->patchJson('/api/links/'.$link->id.'/status', ['status' => 'active', 'version' => 3])->assertOk();
        $this->get('/docs')->assertRedirect();
        $link->update(['expires_at' => now()->subSecond()]);
        $this->get('/docs')->assertStatus(410);
        $link->update(['expires_at' => null]);
        $link->history()->update(['is_redirect_url' => false]);
        $this->get('/docs')->assertStatus(410);
        $this->get('/missing')->assertNotFound();
    }

    public function test_legacy_unsafe_destinations_are_not_redirected(): void
    {
        $link = $this->createLink();
        $link->history()->update(['url' => 'javascript:alert(1)']);
        $this->get('/docs')->assertStatus(410);
        $this->assertDatabaseCount('daily_clicks', 0);
    }

    public function test_search_and_pagination_are_bounded_and_treat_wildcards_literally(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 10) as $i) {
            $this->createLink($user, ['alias' => 'docs-'.$i, 'title' => 'Guide '.$i]);
        }
        $this->actingAs($user)->getJson('/api/links')->assertJsonCount(8, 'data')->assertJsonPath('meta.total', 10);
        $this->getJson('/api/links?page=2')->assertJsonCount(2, 'data');
        $this->getJson('/api/links?search=%25')->assertJsonCount(0, 'data');
        $this->getJson('/api/links?search=docs-10')->assertJsonCount(1, 'data');
        $this->getJson('/api/links?status=invalid')->assertUnprocessable();
    }

    public function test_stats_fill_missing_days_and_exclude_other_owners(): void
    {
        $user = User::factory()->create();
        $link = $this->createLink($user);
        DailyClick::create(['shortlink_id' => $link->id, 'day' => today()->subDays(20)->toDateString(), 'count' => 10]);
        DailyClick::create(['shortlink_id' => $link->id, 'day' => today()->toDateString(), 'count' => 4]);
        $this->actingAs($user)->getJson('/api/stats')->assertOk()->assertJsonPath('total_clicks', 14)->assertJsonPath('recent_clicks', 4)->assertJsonCount(14, 'days')->assertJsonPath('days.0.count', 0)->assertJsonPath('days.13.count', 4);
    }
}
