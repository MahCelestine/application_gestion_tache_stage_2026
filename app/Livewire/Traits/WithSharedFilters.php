<?php 

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use Illuminate\Support\Str;

trait WithSharedFilters
{
    public $search = '';
    public $sortClient = '';
    public $sortTask = '';
    public $sortSubtask = '';

    #[On('search-updated')]
    public function updateSearch($search)
    {
        $this->search = $search;
    }

    #[On('filter-updated')]
    public function updateFilter($name, $value)
    {
        $propertyName = Str::camel($name);
        if (property_exists($this, $propertyName)) {
            $this->{$propertyName} = $value;
        }
    }

    public function sortBy($field)
    {
        $props = 'sort' . ucfirst(($field));

        if (property_exists($this, $props)) {
            $current = $this->{$props};
            $next = match ($current) {
                'asc' => 'desc',
                'desc' => '',
                default => 'asc',
            };
            $this->{$props} = $next;
        }
    }
}