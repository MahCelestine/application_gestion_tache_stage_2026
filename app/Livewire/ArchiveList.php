<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use App\Livewire\Traits\WithSharedFilters;

use Illuminate\Http\Request;

class ArchiveList extends Component
{
    use WithPagination, WithSharedFilters;

    public $search = '';
    public function render()
    {
        $fakeRequest = new Request([
            'search' => $this->search,
            'sort_client' => $this->sortClient,
            'sort_task' => $this->sortTask,
            'sort_subtask' => $this->sortSubtask,
        ]);

        $tasks = Task::archived()
            ->where('is_paid', true)
            ->select(['id', 'client_id', 'label', 'due_date', 'estimated_hours', 'actual_hours', 'quote_number', 'billing_info', 'is_paid', 'status'])
            ->filtersSearch($fakeRequest)
            ->orderBySort($fakeRequest)
            ->with([
                'client:id,nom',
                'subtasks' => fn($q) =>
                    $q->select(['id', 'task_id', 'label', 'due_date', 'status', 'estimated_hours', 'actual_hours', 'quote_number', 'billing_info', 'is_paid'])
                        ->filtersArchive($this->search, $this->sortSubtask)
            ])
            ->paginate(20)
            ->withQueryString();

        return view('livewire.archive-list', [
            'tasks' => $tasks
        ]);
    }
}
