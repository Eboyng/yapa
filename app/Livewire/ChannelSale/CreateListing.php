<?php

namespace App\Livewire\ChannelSale;

use App\Models\ChannelSale;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CreateListing extends Component
{
    use WithFileUploads;

    public string $channel_name = '';
    public string $whatsapp_number = '';
    public string $category = '';
    public int $audience_size = 0;
    public ?float $engagement_rate = null;
    public string $description = '';
    public float $price = 0;
    public array $screenshots = [];
    public bool $visibility = true;

    protected function rules()
    {
        return [
            'channel_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'category' => ['required', Rule::in(array_keys(ChannelSale::CATEGORIES))],
            'audience_size' => 'required|integer|min:1',
            'engagement_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:100',
            'screenshots.*' => 'nullable|image|max:2048', // 2MB max per image
            'visibility' => 'boolean',
        ];
    }

    protected $messages = [
        'channel_name.required' => 'Channel name is required.',
        'whatsapp_number.required' => 'WhatsApp number is required.',
        'category.required' => 'Please select a category.',
        'category.in' => 'Please select a valid category.',
        'audience_size.required' => 'Audience size is required.',
        'audience_size.min' => 'Audience size must be at least 1.',
        'price.required' => 'Price is required.',
        'price.min' => 'Minimum price is ₦100.',
        'screenshots.*.image' => 'Screenshots must be images.',
        'screenshots.*.max' => 'Each screenshot must be less than 2MB.',
    ];

    public function mount()
    {
        // Set default visibility
        $this->visibility = true;
    }

    public function addScreenshot()
    {
        $this->screenshots[] = null;
    }

    public function removeScreenshot($index)
    {
        unset($this->screenshots[$index]);
        $this->screenshots = array_values($this->screenshots);
    }

    public function createListing()
    {
        $this->validate();

        try {
            // Handle screenshot uploads
            $uploadedScreenshots = [];
            foreach ($this->screenshots as $screenshot) {
                if ($screenshot) {
                    $path = $screenshot->store('channel-screenshots', 'public');
                    $uploadedScreenshots[] = $path;
                }
            }

            // Create the channel sale listing
            $channelSale = ChannelSale::create([
                'user_id' => Auth::id(),
                'channel_name' => $this->channel_name,
                'whatsapp_number' => $this->whatsapp_number,
                'category' => $this->category,
                'audience_size' => $this->audience_size,
                'engagement_rate' => $this->engagement_rate,
                'description' => $this->description,
                'price' => $this->price,
                'screenshots' => $uploadedScreenshots,
                'visibility' => $this->visibility,
                'status' => ChannelSale::STATUS_UNDER_REVIEW,
            ]);

            session()->flash('success', 'Your channel listing has been submitted for review. You will be notified once it\'s approved.');
            
            return redirect()->route('channel-sale.my-listings');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create listing. Please try again.');
            \Log::error('Channel sale listing creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.channel-sale.create-listing', [
            'categories' => ChannelSale::CATEGORIES,
        ]);
    }
}