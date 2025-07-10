<?php

namespace App\Livewire\Tips;

use App\Models\Tip;
use Livewire\Component;
use Illuminate\Support\Facades\View;

class ShowTip extends Component
{
    public Tip $tip;

    public function mount(Tip $tip)
    {
        // Ensure the tip is published
        if (!$tip->isPublished()) {
            abort(404);
        }

        $this->tip = $tip;
    }

    public function render()
    {
        // Set meta tags for SEO
        View::share('metaTags', [
            'title' => $this->tip->title,
            'description' => $this->tip->seo_description,
            'image' => $this->tip->image ? asset('storage/' . $this->tip->image) : null,
            'url' => route('tips.show', $this->tip->slug),
        ]);

        return view('livewire.tips.show-tip')
            ->layout('layouts.app')
            ->title($this->tip->title . ' - Tips');
    }
}