<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_hashes_password_and_rotates_session_csrf(): void
    {
        config(['links.registration_enabled' => true]);
        $before = $this->getJson('/api/session')->json('csrf_token');
        $s = $this->postJson('/api/register', ['name' => 'Alex', 'email' => 'alex@example.test', 'password' => 'a-long-passphrase', 'password_confirmation' => 'a-long-passphrase'])->assertCreated()->assertJsonMissingPath('user.password');
        $this->assertAuthenticated();
        $this->assertTrue(Hash::check('a-long-passphrase', User::first()->password));
        $this->assertNotSame($before, $s->json('csrf_token'));
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->postJson('/api/logout')->assertOk();
        $this->assertGuest();
    }

    public function test_credentials_work_and_guest_legacy_accounts_cannot_login(): void
    {
        $user = User::factory()->create(['email' => 'alex@example.test']);
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong'])->assertUnprocessable();
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'test-password-123'])->assertOk();
        $this->assertAuthenticatedAs($user);
        $this->postJson('/api/logout')->assertOk();
        $user->update(['guest' => true]);
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'test-password-123'])->assertUnprocessable();
    }

    public function test_registration_can_be_disabled_and_rejects_weak_passwords(): void
    {
        config(['links.registration_enabled' => false]);
        $this->postJson('/api/register', [])->assertForbidden();
        config(['links.registration_enabled' => true]);
        $this->postJson('/api/register', ['name' => 'A', 'email' => 'a@example.test', 'password' => 'short', 'password_confirmation' => 'short'])->assertUnprocessable();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_browser_mutations_require_csrf(): void
    {
        $this->app['env'] = 'local';
        $this->postJson('/api/login', ['email' => 'a@example.test', 'password' => 'test-password-123'])->assertStatus(419);
        $this->actingAs(User::factory()->create())->postJson('/api/links', ['title' => 'X', 'url' => 'https://example.com'])->assertStatus(419);
    }

    public function test_login_rate_limit_and_legacy_token_rejection(): void
    {
        foreach (range(1, 5) as $i) {
            $this->postJson('/api/login', ['email' => 'a@example.test', 'password' => 'wrong'])->assertUnprocessable();
        }
        $this->postJson('/api/login', ['email' => 'a@example.test', 'password' => 'wrong'])->assertStatus(429);
        $user = User::factory()->create();
        DB::table('personal_access_tokens')->insert(['tokenable_type' => User::class, 'tokenable_id' => $user->id, 'name' => 'auth', 'token' => hash('sha256', 'old'), 'abilities' => '["*"]']);
        $this->withToken('old')->getJson('/api/links')->assertUnauthorized();
    }
}
