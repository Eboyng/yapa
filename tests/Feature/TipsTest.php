<?php

namespace Tests\Feature;

use App\Models\Tip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class TipsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role if it doesn't exist
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
    }

    /** @test */
    public function guests_can_view_tip_list()
    {
        // Create published tips
        $publishedTip = Tip::factory()->published()->create();
        $draftTip = Tip::factory()->draft()->create();

        $response = $this->get(route('tips.index'));

        $response->assertStatus(200);
        $response->assertSee($publishedTip->title);
        $response->assertDontSee($draftTip->title);
    }

    /** @test */
    public function guests_can_view_individual_published_tips()
    {
        $tip = Tip::factory()->published()->create();

        $response = $this->get(route('tips.show', $tip->slug));

        $response->assertStatus(200);
        $response->assertSee($tip->title);
        $response->assertSee($tip->content);
    }

    /** @test */
    public function guests_cannot_view_draft_tips()
    {
        $tip = Tip::factory()->draft()->create();

        $response = $this->get(route('tips.show', $tip->slug));

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_users_can_clap_for_tips()
    {
        $user = User::factory()->create();
        $tip = Tip::factory()->published()->create(['claps' => 0]);

        $this->actingAs($user);

        Livewire::test('tips.clap', ['tip' => $tip])
            ->call('clap')
            ->assertSet('tip.claps', 1)
            ->assertSet('hasClapped', true);

        $this->assertDatabaseHas('tips', [
            'id' => $tip->id,
            'claps' => 1
        ]);
    }

    /** @test */
    public function users_cannot_clap_multiple_times_in_same_session()
    {
        $user = User::factory()->create();
        $tip = Tip::factory()->published()->create(['claps' => 0]);

        $this->actingAs($user);

        $component = Livewire::test('tips.clap', ['tip' => $tip]);
        
        // First clap
        $component->call('clap')
            ->assertSet('tip.claps', 1)
            ->assertSet('hasClapped', true);

        // Second clap should not increase count
        $component->call('clap')
            ->assertSet('tip.claps', 1)
            ->assertSet('hasClapped', true);

        $this->assertDatabaseHas('tips', [
            'id' => $tip->id,
            'claps' => 1
        ]);
    }

    /** @test */
    public function admin_can_create_tip()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $tipData = [
            'title' => 'Test Tip Title',
            'slug' => 'test-tip-title',
            'content' => 'This is a test tip content that should be at least 50 characters long to pass validation.',
            'author_id' => $admin->id,
            'status' => Tip::STATUS_DRAFT,
        ];

        $response = $this->post('/admin/tips', $tipData);

        $this->assertDatabaseHas('tips', [
            'title' => 'Test Tip Title',
            'slug' => 'test-tip-title',
            'status' => Tip::STATUS_DRAFT,
            'author_id' => $admin->id,
        ]);
    }

    /** @test */
    public function admin_can_publish_tip()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $tip = Tip::factory()->draft()->create(['author_id' => $admin->id]);

        $this->actingAs($admin);

        $tip->markAsPublished();
        $tip->update(['published_at' => now()]);

        $this->assertDatabaseHas('tips', [
            'id' => $tip->id,
            'status' => Tip::STATUS_PUBLISHED,
        ]);

        $this->assertNotNull($tip->fresh()->published_at);
    }

    /** @test */
    public function non_admin_users_cannot_access_tip_admin_interface()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/admin/tips');

        $response->assertStatus(403);
    }

    /** @test */
    public function tip_content_validation_requires_minimum_length()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $tipData = [
            'title' => 'Test Tip',
            'slug' => 'test-tip',
            'content' => 'Short', // Too short
            'author_id' => $admin->id,
            'status' => Tip::STATUS_DRAFT,
        ];

        $response = $this->post('/admin/tips', $tipData);

        $response->assertSessionHasErrors('content');
    }

    /** @test */
    public function tip_slug_must_be_unique()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create existing tip
        Tip::factory()->create(['slug' => 'existing-slug']);

        $this->actingAs($admin);

        $tipData = [
            'title' => 'New Tip',
            'slug' => 'existing-slug', // Duplicate slug
            'content' => 'This is a new tip with duplicate slug that should fail validation.',
            'author_id' => $admin->id,
            'status' => Tip::STATUS_DRAFT,
        ];

        $response = $this->post('/admin/tips', $tipData);

        $response->assertSessionHasErrors('slug');
    }

    /** @test */
    public function only_published_tips_appear_in_listing()
    {
        $publishedTip = Tip::factory()->published()->create();
        $draftTip = Tip::factory()->draft()->create();
        $archivedTip = Tip::factory()->archived()->create();
        $scheduledTip = Tip::factory()->published()->create([
            'published_at' => now()->addDay()
        ]);

        Livewire::test('tips.list-tips')
            ->assertSee($publishedTip->title)
            ->assertDontSee($draftTip->title)
            ->assertDontSee($archivedTip->title)
            ->assertDontSee($scheduledTip->title);
    }

    /** @test */
    public function tip_search_functionality_works()
    {
        $tip1 = Tip::factory()->published()->create(['title' => 'Business Growth Tips']);
        $tip2 = Tip::factory()->published()->create(['title' => 'Marketing Strategies']);

        Livewire::test('tips.list-tips')
            ->set('search', 'Business')
            ->assertSee($tip1->title)
            ->assertDontSee($tip2->title);
    }

    /** @test */
    public function tip_content_preview_is_limited()
    {
        $longContent = str_repeat('This is a very long content. ', 50);
        $tip = Tip::factory()->published()->create(['content' => $longContent]);

        $preview = $tip->content_preview;

        $this->assertTrue(strlen($preview) <= 300);
        $this->assertStringEndsWith('...', $preview);
    }
}