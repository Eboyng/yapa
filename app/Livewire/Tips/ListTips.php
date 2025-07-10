<?php

namespace App\Livewire\Tips;

use App\Models\Tip;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ListTips extends Component
{
    use WithPagination;

    public $search = '';
    public $expandedTips = [];
    public $suggestions = [];
    public $showSuggestions = false;
    public $perPage = 10;
    public $hasMorePages = true;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadSuggestions();
    }

    public function loadSuggestions()
    {
        if (strlen($this->search) >= 2) {
            $this->suggestions = Tip::published()
                ->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('content', 'like', '%' . $this->search . '%');
                })
                ->limit(5)
                ->pluck('title')
                ->toArray();
            $this->showSuggestions = count($this->suggestions) > 0;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function selectSuggestion($suggestion)
    {
        $this->search = $suggestion;
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function hideSuggestions()
    {
        $this->showSuggestions = false;
    }

    public function toggleExpanded($tipId)
    {
        if (in_array($tipId, $this->expandedTips)) {
            $this->expandedTips = array_filter($this->expandedTips, fn($id) => $id !== $tipId);
        } else {
            $this->expandedTips[] = $tipId;
        }
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    #[On('scroll-to-load')]
    public function handleScrollLoad()
    {
        if ($this->hasMorePages) {
            $this->loadMore();
        }
    }

    public function render()
    {
        $query = Tip::published()
            ->with('author')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc');

        $totalCount = $query->count();
        $tips = $query->take($this->perPage)->get();
        
        $this->hasMorePages = $this->perPage < $totalCount;

        return view('livewire.tips.list-tips', compact('tips'))
            ->layout('layouts.app')
            ->title('Tips - Inspirational & Business Insights');
    }
}