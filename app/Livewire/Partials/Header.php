<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public function getCreditsBalanceProperty()
    {
        if (!Auth::check()) {
            return 0;
        }
        return Auth::user()->getWallet('credits')->balance ?? 0;
    }

    public function getNairaBalanceProperty()
    {
        if (!Auth::check()) {
            return 0;
        }
        return Auth::user()->getWallet('naira')->balance ?? 0;
    }

    public function getEarningsBalanceProperty()
    {
        if (!Auth::check()) {
            return 0;
        }
        return Auth::user()->getWallet('earnings')->balance ?? 0;
    }

    public function render()
    {
        return view('livewire.partials.header');
    }
}