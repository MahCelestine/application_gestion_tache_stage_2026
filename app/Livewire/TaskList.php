<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Livewire\Traits\WithSharedFilters;


class TaskList extends Component
{
    use WithSharedFilters;
    public $isCca = false;
    public $filterStatus = '';

    public function mount($isCca = false)
    {
        $this->isCca = $isCca;
        $this->filterStatus = request('filter_status', '');
    }
    public function render()
    {
        $fakeRequest = new Request([
            'search' => $this->search,
            'sort_client' => $this->sortClient,
            'sort_task' => $this->sortTask,
            'sort_subtask' => $this->sortSubtask,
            'filter_status' => $this->filterStatus,
        ]);

        $query = Task::with([
            'client',
            'equipes',
            'subtasks' => function ($q) {
                $q->with(['equipes', 'reasons']);

                if ($this->filterStatus) {
                    $q->where('status', $this->filterStatus);
                }

                if ($this->sortSubtask) {
                    $q->orderBy('label', $this->sortSubtask);
                }

                $q->filtersSortSub(null, $this->filterStatus, $this->sortSubtask);
            }
        ]);

        $query->clientCCA($this->isCca);

        $query->filtersSearch($fakeRequest)->orderBySort($fakeRequest);

        if (!$this->search && !$this->filterStatus) {
            $query->where('status', '!=', 'validé');
        }

        return view('livewire.task-list', [
            'tasks' => $query->get()
        ]);
    }

}