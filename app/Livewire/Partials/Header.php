<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;

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
        return Auth::user()->getWallet(Wallet::TYPE_NAIRA)->balance ?? 0;
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