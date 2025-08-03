<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use App\Livewire\Profile;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $settingService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'whatsapp_number' => '+2348123456789',
            'google_access_token' => null,
            'google_refresh_token' => null,
        ]);
        
        $this->settingService = app(SettingService::class);
        
        // Set up Google OAuth settings for testing
        $this->settingService->setMultiple([
            'google_oauth_enabled' => true,
            'google_client_id' => 'test_client_id',
            'google_client_secret' => 'test_client_secret',
            'google_redirect_uri' => 'http://localhost/google/callback',
            'google_scopes' => 'https://www.googleapis.com/auth/contacts.readonly openid profile email'
        ]);
    }

    /** @test */
    public function it_can_initiate_google_oauth_connection()
    {
        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->call('connectGoogle')
            ->assertDispatched('google-oauth-redirect');
    }

    /** @test */
    public function it_fails_when_google_oauth_is_disabled()
    {
        $this->settingService->set('google_oauth_enabled', false);
        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->call('connectGoogle')
            ->assertDispatched('google-oauth-error', function ($event) {
                return str_contains($event['message'], 'Google OAuth is currently disabled');
            });
    }

    /** @test */
    public function it_fails_when_client_id_is_missing()
    {
        $this->settingService->set('google_client_id', '');
        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->call('connectGoogle')
            ->assertDispatched('google-oauth-error', function ($event) {
                return str_contains($event['message'], 'Missing Client ID');
            });
    }

    /** @test */
    public function it_fails_when_client_secret_is_missing()
    {
        $this->settingService->set('google_client_secret', '');
        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->call('connectGoogle')
            ->assertDispatched('google-oauth-error', function ($event) {
                return str_contains($event['message'], 'Missing Client Secret');
            });
    }

    /** @test */
    public function it_can_handle_google_oauth_callback_success()
    {
        // Mock successful token exchange
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'expires_in' => 3600,
                'token_type' => 'Bearer'
            ], 200)
        ]);

        $state = base64_encode(json_encode([
            'user_id' => $this->user->id,
            'action' => 'connect',
            'timestamp' => time()
        ]));

        $response = $this->actingAs($this->user)
            ->get(route('google.callback', [
                'code' => 'test_authorization_code',
                'state' => $state
            ]));

        $response->assertRedirect(route('profile'))
            ->assertSessionHas('success', 'Google account connected successfully!');

        $this->user->refresh();
        $this->assertEquals('test_access_token', $this->user->google_access_token);
        $this->assertEquals('test_refresh_token', $this->user->google_refresh_token);
    }

    /** @test */
    public function it_handles_google_oauth_callback_error()
    {
        $response = $this->actingAs($this->user)
            ->get(route('google.callback', [
                'error' => 'access_denied',
                'error_description' => 'User denied access'
            ]));

        $response->assertRedirect(route('profile'))
            ->assertSessionHas('error', 'Google authentication was cancelled by user');
    }

    /** @test */
    public function it_handles_invalid_state_in_callback()
    {
        $response = $this->actingAs($this->user)
            ->get(route('google.callback', [
                'code' => 'test_code',
                'state' => 'invalid_state'
            ]));

        $response->assertRedirect(route('profile'))
            ->assertSessionHas('error', 'Invalid OAuth state. Please try again.');
    }

    /** @test */
    public function it_handles_token_exchange_failure()
    {
        // Mock failed token exchange
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid authorization code'
            ], 400)
        ]);

        $state = base64_encode(json_encode([
            'user_id' => $this->user->id,
            'action' => 'connect',
            'timestamp' => time()
        ]));

        $response = $this->actingAs($this->user)
            ->get(route('google.callback', [
                'code' => 'invalid_code',
                'state' => $state
            ]));

        $response->assertRedirect(route('profile'))
            ->assertSessionHas('error', 'Failed to authenticate with Google. Please try again.');
    }

    /** @test */
    public function it_can_disconnect_google_account()
    {
        // Set up user with connected Google account
        $this->user->update([
            'google_access_token' => 'existing_token',
            'google_refresh_token' => 'existing_refresh_token'
        ]);

        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->call('disconnectGoogle')
            ->assertDispatched('google-oauth-success', function ($event) {
                return str_contains($event['message'], 'disconnected successfully');
            });

        $this->user->refresh();
        $this->assertNull($this->user->google_access_token);
        $this->assertNull($this->user->google_refresh_token);
    }

    /** @test */
    public function it_logs_oauth_events_properly()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Google OAuth connection attempt started', \Mockery::type('array'));

        Log::shouldReceive('info')
            ->once()
            ->with('Google OAuth settings retrieved', \Mockery::type('array'));

        Log::shouldReceive('info')
            ->once()
            ->with('Google OAuth URL generated successfully', \Mockery::type('array'));

        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->call('connectGoogle');
    }

    /** @test */
    public function it_shows_correct_connection_status_in_profile()
    {
        $this->actingAs($this->user);

        // Test disconnected state
        Livewire::test(Profile::class)
            ->assertSee('Connect your Google account')
            ->assertSee('Connect Google');

        // Test connected state
        $this->user->update(['google_access_token' => 'test_token']);

        Livewire::test(Profile::class)
            ->assertSee('Your Google account is connected')
            ->assertSee('Disconnect');
    }
}
