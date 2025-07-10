<?php

namespace Tests\Feature;

use App\Models\ChannelSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChannelSaleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_create_channel_sale_listing()
    {
        $user = User::factory()->create();
        
        $channelData = [
            'channel_name' => 'Test Channel',
            'whatsapp_number' => '+2348012345678',
            'category' => 'entertainment',
            'audience_size' => 5000,
            'engagement_rate' => 15.5,
            'description' => 'A great entertainment channel',
            'price' => 50000.00,
            'visibility' => true,
        ];
        
        $channelSale = $user->channelSales()->create($channelData);
        
        $this->assertDatabaseHas('channel_sales', [
            'user_id' => $user->id,
            'channel_name' => 'Test Channel',
            'price' => 50000.00,
            'status' => ChannelSale::STATUS_UNDER_REVIEW,
        ]);
        
        $this->assertEquals($user->id, $channelSale->user_id);
        $this->assertEquals('Test Channel', $channelSale->channel_name);
        $this->assertTrue($channelSale->isUnderReview());
    }
    
    public function test_user_can_view_their_channel_sale_listings()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        // Create listings for the user
        $userListings = ChannelSale::factory()->count(3)->create(['user_id' => $user->id]);
        
        // Create listings for another user
        ChannelSale::factory()->count(2)->create(['user_id' => $otherUser->id]);
        
        $retrievedListings = $user->channelSales;
        
        $this->assertCount(3, $retrievedListings);
        $this->assertTrue($retrievedListings->contains($userListings[0]));
        $this->assertTrue($retrievedListings->contains($userListings[1]));
        $this->assertTrue($retrievedListings->contains($userListings[2]));
    }
    
    public function test_channel_sale_status_transitions()
    {
        $channelSale = ChannelSale::factory()->underReview()->create();
        
        // Test marking as listed
        $channelSale->markAsListed();
        $this->assertTrue($channelSale->isListed());
        $this->assertEquals(ChannelSale::STATUS_LISTED, $channelSale->status);
        
        // Test marking as sold
        $channelSale->markAsSold();
        $this->assertTrue($channelSale->isSold());
        $this->assertEquals(ChannelSale::STATUS_SOLD, $channelSale->status);
        
        // Create another channel for removal test
        $anotherChannel = ChannelSale::factory()->listed()->create();
        $anotherChannel->markAsRemoved();
        $this->assertTrue($anotherChannel->isRemoved());
        $this->assertEquals(ChannelSale::STATUS_REMOVED, $anotherChannel->status);
    }
    
    public function test_channel_sale_visibility_scope()
    {
        // Create visible and hidden channels
        ChannelSale::factory()->count(3)->create(['visibility' => true, 'status' => ChannelSale::STATUS_LISTED]);
        ChannelSale::factory()->count(2)->create(['visibility' => false, 'status' => ChannelSale::STATUS_LISTED]);
        
        $visibleChannels = ChannelSale::visible()->get();
        $this->assertCount(3, $visibleChannels);
        
        foreach ($visibleChannels as $channel) {
            $this->assertTrue($channel->visibility);
        }
    }
    
    public function test_channel_sale_listed_scope()
    {
        // Create channels with different statuses
        ChannelSale::factory()->count(2)->listed()->create();
        ChannelSale::factory()->count(1)->underReview()->create();
        ChannelSale::factory()->count(1)->sold()->create();
        ChannelSale::factory()->count(1)->removed()->create();
        
        $listedChannels = ChannelSale::listed()->get();
        $this->assertCount(2, $listedChannels);
        
        foreach ($listedChannels as $channel) {
            $this->assertEquals(ChannelSale::STATUS_LISTED, $channel->status);
        }
    }
    
    public function test_channel_sale_available_scope()
    {
        // Create available channels (listed and visible)
        ChannelSale::factory()->count(3)->create([
            'status' => ChannelSale::STATUS_LISTED,
            'visibility' => true,
        ]);
        
        // Create unavailable channels
        ChannelSale::factory()->count(1)->create([
            'status' => ChannelSale::STATUS_UNDER_REVIEW,
            'visibility' => true,
        ]);
        ChannelSale::factory()->count(1)->create([
            'status' => ChannelSale::STATUS_LISTED,
            'visibility' => false,
        ]);
        ChannelSale::factory()->count(1)->sold()->create();
        
        $availableChannels = ChannelSale::available()->get();
        $this->assertCount(3, $availableChannels);
        
        foreach ($availableChannels as $channel) {
            $this->assertEquals(ChannelSale::STATUS_LISTED, $channel->status);
            $this->assertTrue($channel->visibility);
        }
    }
    
    public function test_channel_sale_price_filtering()
    {
        // Create channels with different prices
        ChannelSale::factory()->create(['price' => 5000, 'status' => ChannelSale::STATUS_LISTED]);
        ChannelSale::factory()->create(['price' => 15000, 'status' => ChannelSale::STATUS_LISTED]);
        ChannelSale::factory()->create(['price' => 25000, 'status' => ChannelSale::STATUS_LISTED]);
        ChannelSale::factory()->create(['price' => 35000, 'status' => ChannelSale::STATUS_LISTED]);
        
        // Test price range filtering
        $channelsInRange = ChannelSale::listed()
            ->whereBetween('price', [10000, 30000])
            ->get();
            
        $this->assertCount(2, $channelsInRange);
        
        foreach ($channelsInRange as $channel) {
            $this->assertGreaterThanOrEqual(10000, $channel->price);
            $this->assertLessThanOrEqual(30000, $channel->price);
        }
    }
    
    public function test_channel_sale_category_filtering()
    {
        // Create channels with different categories
        ChannelSale::factory()->count(2)->create([
            'category' => 'entertainment',
            'status' => ChannelSale::STATUS_LISTED,
        ]);
        ChannelSale::factory()->count(1)->create([
            'category' => 'education',
            'status' => ChannelSale::STATUS_LISTED,
        ]);
        ChannelSale::factory()->count(1)->create([
            'category' => 'business',
            'status' => ChannelSale::STATUS_LISTED,
        ]);
        
        $entertainmentChannels = ChannelSale::listed()
            ->where('category', 'entertainment')
            ->get();
            
        $this->assertCount(2, $entertainmentChannels);
        
        foreach ($entertainmentChannels as $channel) {
            $this->assertEquals('entertainment', $channel->category);
        }
    }
}