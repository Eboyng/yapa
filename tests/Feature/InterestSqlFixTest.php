<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Interest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Profile;

class InterestSqlFixTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_interests_can_be_loaded_without_sql_ambiguity_error()
    {
        // Create test user and interests
        $user = User::factory()->create();
        $interests = Interest::factory()->count(3)->create();
        
        // Attach interests to user
        $user->interests()->sync($interests->pluck('id')->toArray());
        
        $this->actingAs($user);

        // This should not throw an SQL ambiguity error
        $component = Livewire::test(Profile::class);
        
        // Verify the component loads successfully
        $component->assertOk();
        
        // Verify interests are loaded correctly
        $selectedInterests = $component->get('selectedInterests');
        $this->assertCount(3, $selectedInterests);
        $this->assertEquals($interests->pluck('id')->sort()->values(), 
                           collect($selectedInterests)->sort()->values());
    }

    /** @test */
    public function toggle_interest_edit_works_without_sql_errors()
    {
        // Create test user and interests
        $user = User::factory()->create();
        $interests = Interest::factory()->count(2)->create();
        
        // Attach interests to user
        $user->interests()->sync($interests->pluck('id')->toArray());
        
        $this->actingAs($user);

        // This should not throw an SQL ambiguity error when toggling edit mode
        $component = Livewire::test(Profile::class);
        
        // Toggle edit mode on and off - this triggers the interest reload
        $component->call('toggleInterestEdit'); // Turn on edit mode
        $component->call('toggleInterestEdit'); // Turn off edit mode (triggers reload)
        
        // Verify no errors occurred
        $component->assertOk();
        
        // Verify interests are still loaded correctly after toggle
        $selectedInterests = $component->get('selectedInterests');
        $this->assertCount(2, $selectedInterests);
    }

    /** @test */
    public function user_interests_property_works_without_sql_errors()
    {
        // Create test user and interests
        $user = User::factory()->create();
        $interests = Interest::factory()->count(4)->create();
        
        // Attach interests to user
        $user->interests()->sync($interests->pluck('id')->toArray());
        
        $this->actingAs($user);

        // Test the userInterests computed property
        $component = Livewire::test(Profile::class);
        
        // This should not throw an SQL ambiguity error
        $userInterests = $component->get('userInterests');
        
        // Verify the property returns the correct data
        $this->assertCount(4, $userInterests);
        $this->assertEquals($interests->pluck('id')->sort()->values(), 
                           collect($userInterests)->pluck('id')->sort()->values());
    }
}
