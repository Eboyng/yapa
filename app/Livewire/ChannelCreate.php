<?php

namespace App\Livewire;

use App\Models\Channel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChannelCreate extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $niche = '';
    public int $follower_count = 0;
    public string $whatsapp_link = '';
    public string $description = '';
    public $sample_screenshot;
    public bool $isSubmitting = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'niche' => 'required|string|in:technology,business,entertainment,sports,news,education,lifestyle,health,finance,travel,food,fashion,music,gaming,other',
        'follower_count' => 'required|integer|min:100',
        'whatsapp_link' => 'required|url|regex:/^https:\/\/(chat\.whatsapp\.com|wa\.me)\/.+/',
        'description' => 'required|string|min:50|max:1000',
        'sample_screenshot' => 'required|image|max:2048', // 2MB max
    ];

    protected $messages = [
        'name.required' => 'Channel name is required.',
        'niche.required' => 'Please select a niche.',
        'niche.in' => 'Please select a valid niche.',
        'follower_count.required' => 'Follower count is required.',
        'follower_count.min' => 'Minimum follower count is 100.',
        'whatsapp_link.required' => 'WhatsApp link is required.',
        'whatsapp_link.url' => 'Please enter a valid URL.',
        'whatsapp_link.regex' => 'Please enter a valid WhatsApp group or channel link.',
        'description.required' => 'Description is required.',
        'description.min' => 'Description must be at least 50 characters.',
        'description.max' => 'Description cannot exceed 1000 characters.',
        'sample_screenshot.required' => 'Sample screenshot is required.',
        'sample_screenshot.image' => 'File must be an image.',
        'sample_screenshot.max' => 'Image size cannot exceed 2MB.',
    ];

    public function submit()
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->validate();

            // Check if user already has a pending or approved channel
            $existingChannel = Channel::where('user_id', Auth::id())
                ->whereIn('status', [Channel::STATUS_PENDING, Channel::STATUS_APPROVED])
                ->first();

            if ($existingChannel) {
                session()->flash('error', 'You already have a channel that is pending approval or approved.');
                return;
            }

            // Upload screenshot
            $screenshotPath = $this->sample_screenshot->store('channel-screenshots', 'public');

            // Create channel
            $channel = Channel::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'niche' => $this->niche,
                'follower_count' => $this->follower_count,
                'whatsapp_link' => $this->whatsapp_link,
                'description' => $this->description,
                'sample_screenshot' => $screenshotPath,
                'status' => Channel::STATUS_PENDING,
            ]);

            session()->flash('success', 'Channel submitted successfully! It will be reviewed by our team.');
            
            // Reset form
            $this->reset([
                'name', 'niche', 'follower_count', 'whatsapp_link', 
                'description', 'sample_screenshot'
            ]);

            // Redirect to channel list or dashboard
            return redirect()->route('channels.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit channel: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.channel-create', [
            'niches' => Channel::NICHES,
        ]);
    }
}