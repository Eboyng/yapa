<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\User;

class Layout extends Component
{
    public User $user;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('livewire.profile.layout');
    }
}