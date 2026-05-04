<?php

namespace App\Livewire;

use Livewire\Component;
use function Laravel\Prompts\search;

class SearchBar extends Component
{
    public $search = '';
    public $placeholder = 'Rechercher...';

    public function mount($placeholder = null)
    {
        $this->search = request('search', '');
        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }

    public function updatedSearch()
    {
        $this->dispatch('search-updated', search: $this->search);
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}