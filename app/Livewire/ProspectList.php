<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Http\Request;
use App\Models\Prospect;

class ProspectList extends Component
{
    public $search = '';
    public $filterStatus = '';
    public $sortNom = '';

    public function sortByNom() {
        $this->sortNom = match ($this->sortNom) {
            'asc' => 'desc',
            'desc' => '',
            default => 'asc',
        };
        $this->resetPage();
    }

    #[On('search-updated')]
    public function updateSearch($search)
    {
        $this->search = $search;
    }

    #[On('filter-updated')]
    public function updateFilter($name, $value)
    {
        if ($name === 'filter_status') {
            $this->filterStatus = $value;
        }
    }

    public function render()
    {
        $fakeRequest = new Request([
            'search' => $this->search,
            'filter_status' => $this->filterStatus,
            'sort_nom' => $this->sortNom,
        ]);
        $query = Prospect::with('notes');

        if($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where('nom', 'LIKE', "%{$this->search}%");
        };

        $query->orderByRaw("
        CASE
        WHEN rdv_date IS NOT NULL THEN 1
        WHEN status = 'RDV à prendre' THEN 2
        WHEN status = 'Date de RDV' THEN 3
        WHEN status = 'OK' THEN 4
        ELSE 5
        END ASC
        ");

        $query->orderByRaw("
        CASE
        WHEN status = 'OK' AND is_followup = 'NON' THEN 1
        WHEN status = 'OK' AND is_followup = 'OUI' THEN 2
        ELSE 3
        END ASC
        ");

        if ($this->sortNom) {
            $query->orderBy('nom', $this->sortNom);
        } else {
            $query->orderBy('rdv_date', 'asc');
        }

        return view('livewire.prospect-list', [
            'prospects' => $query->get(),
        ]);
    }
}
