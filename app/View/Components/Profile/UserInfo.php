<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\AvatarService;

class UserInfo extends Component
{
    public $user;
    public $avatarUrl;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
        
        $avatarService = app(AvatarService::class);
        $this->avatarUrl = $avatarService->generateAvatarUrl($this->user, [
            'size' => 200,
            'background' => 'EBF4FF',
            'color' => '7F9CF5'
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.profile.user-info');
    }
}