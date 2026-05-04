<?php

namespace App\Livewire;

use Livewire\Component;

class LoadingOverlay extends Component
{
    public function render()
    {
        return view('livewire.loading-overlay');
    }
}
