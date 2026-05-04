<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\On;
use Illuminate\Http\Request;
use App\Livewire\Traits\WithSharedFilters;

class GestionList extends Component
{

    use WithSharedFilters;
    public $isCca = false;
    public $filterPayment = '';

    public function mount($isCca = false)
    {
        $this->isCca = $isCca;
        $this->filterPayment = request('filter_payment', '');
    }   
    public function render()
    {
        $fakeRequest = new Request([
            'search' => $this->search,
            'filter_payment' => $this->filterPayment,
            'sort_client' => $this->sortClient,
            'sort_task' => $this->sortTask,
            'sort_subtask' => $this->sortSubtask,
        ]);

        $tasks = Task::clientCCA(false)
            ->where(fn($q) => $q->where('status', 'validé')->orWhereHas('subtasks', fn($sq) => $sq->where('status', 'validé')))
            ->where(fn($q) => $q->where('is_paid', false)->orWhereHas('subtasks', fn($sq) => $sq->where('is_paid', false)))
            ->select(['id', 'client_id', 'label', 'status', 'quote_number', 'billing_info', 'is_paid'])
            ->filtersSearch($fakeRequest)
            ->filtersPaid($this->filterPayment)
            ->orderBySort($fakeRequest)
            ->with([
                'client:id,nom',
                'subtasks' => fn($q) =>
                    $q->select(['id', 'task_id', 'label', 'status', 'quote_number', 'billing_info', 'is_paid'])
                        ->filtersPaidSub(
                            $this->search,
                            $this->filterPayment,
                            $this->sortSubtask
                        )
            ])
            ->get();

        return view('livewire.gestion-list', [
            'tasks' => $tasks,
        ]);
    }
}
