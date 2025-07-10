<?php

namespace App\Livewire\Tips;

use App\Models\Tip;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Clap extends Component
{
    public $tip;
    public $hasClapped = false;
    public $isAnimating = false;

    public function mount(Tip $tip)
    {
        $this->tip = $tip;
        $this->hasClapped = $this->checkIfUserHasClapped();
    }

    public function clap()
    {
        if ($this->hasClapped) {
            return;
        }

        $this->tip->incrementClaps();
        $this->tip->refresh();
        
        // Mark as clapped in session to prevent duplicate claps
        $clappedTips = Session::get('clapped_tips', []);
        $clappedTips[] = $this->tip->id;
        Session::put('clapped_tips', $clappedTips);
        
        $this->hasClapped = true;
        $this->isAnimating = true;
        
        // Reset animation after a short delay
        $this->dispatch('clap-animated');
    }

    public function resetAnimation()
    {
        $this->isAnimating = false;
    }

    private function checkIfUserHasClapped(): bool
    {
        $clappedTips = Session::get('clapped_tips', []);
        return in_array($this->tip->id, $clappedTips);
    }

    public function render()
    {
        return view('livewire.tips.clap');
    }
}