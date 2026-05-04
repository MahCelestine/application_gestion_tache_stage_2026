<?php

namespace App\Livewire;

use Livewire\Component;

class TaskFilters extends Component
{
    public $name;

    public $label;

    public $options = [];

    public $current = '';

    public function mount($name, $label, $options, $current)
    {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->current = $current ?? request($this->name, '');
    }

    public function setFilter ($value) {
        $this->current = $value;
        $this->dispatch('filter-updated', name: $this->name, value: $value);
    }

    public function render()
    {
        return view('livewire.task-filters');
    }
}
