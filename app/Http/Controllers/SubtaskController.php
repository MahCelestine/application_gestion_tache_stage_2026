<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipe;
use App\Models\Subtask;
use Carbon\Carbon;

class SubtaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'label' => 'required|string|max:255',
            'due_date' => 'required|date',
            'estimated_hours' => 'required|numeric',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'equipe_ids' => 'nullable|array',
        ]);

        $subtask = Subtask::create([
            'task_id' => $validated['task_id'],
            'label' => $validated['label'],
            'due_date' => $validated['due_date'],
            'estimated_hours' => $validated['estimated_hours'],
            'quote_number' => $validated['quote_number'],
            'billing_info' => $validated['billing_info'],
            'actual_hours' => 0,
        ]);

        if ($request->has('equipe_ids')) {
            $subtask->equipes()->sync($request->equipe_ids);
        }

        $parentTask = $subtask->task;
        $TotalSubtasksEstimatedHours = $parentTask->subtasks()->sum('estimated_hours');

        if ($TotalSubtasksEstimatedHours > $parentTask->estimated_hours) {
            $parentTask->update([
                'estimated_hours' => $TotalSubtasksEstimatedHours
            ]);
        }

        return redirect()->route('tasks.index');
    }

    public function edit($id)
    {
        $subtask = Subtask::findOrFail($id);
        $equipes = Equipe::all();

        return view('tasks.form_edit_subtask', compact('subtask', 'equipes'));
    }

    public function update(Request $request, $id)
    {
        $subtask = Subtask::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'status' => 'required|in:en cours,validé,bloqué',
            'reason_description' => 'nullable|string|required_if:status,bloqué',
            'estimated_hours' => 'required|numeric',
            'hours_to_add' => 'nullable|numeric',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'equipe_ids' => 'nullable|array',
        ]);

        if ($validated['status'] == 'bloqué') {
            $currentBlocking = $subtask->currentBlocking();

            if ($currentBlocking) {
                $currentBlocking->update([
                    'description' => $validated['reason_description']
                ]);
            } else {
                $subtask->reasons()->create([
                    'description' => $validated['reason_description'],
                    'is_finish' => false,
                ]);
            }
        }
        $newTotalHours = $subtask->actual_hours + ($request->hours_to_add ?? 0);

        $subtask->update([
            'status' => $validated['status'],
            'label' => $validated['label'],
            'estimated_hours' => $validated['estimated_hours'],
            'due_date' => $validated['due_date'],
            'quote_number' => $validated['quote_number'],
            'billing_info' => $validated['billing_info'],
            'actual_hours' => $newTotalHours,
        ]);

        $parentTask = $subtask->task;
        $remainingSubtasks = $parentTask->subtasks()->where('status', '!=', 'validé')->count();
        $TotalActualHours = $parentTask->subtasks()->sum('actual_hours');

        $parentTask->update([
            'actual_hours' => $TotalActualHours
        ]);

        if (Carbon::parse($parentTask->due_date)->lt(Carbon::parse($subtask->due_date))) {
            $parentTask->update(['due_date' => $subtask->due_date]);
        }

        $remainingSubtasks = $parentTask->subtasks()->where('status', '!=', 'validé')->count();
        $blockedSubtasks = $parentTask->subtasks()->where('status', 'bloqué')->count();

        if ($blockedSubtasks > 0) {
            $parentTask->update(['status' => 'bloqué']);
        } elseif ($remainingSubtasks === 0) {
            $parentTask->update(['status' => 'validé']);
        } else {
            $parentTask->update(['status' => 'en cours']);

            if ($validated['status'] !== 'bloqué') {
                $subtask->reasons()->where('is_finish', false)->update(['is_finish' => true]);
            }
        }

        $subtask->equipes()->sync($request->equipe_ids ?? []);

        return redirect()->route('tasks.index');
    }

    public function create()
    {
        $equipes = Equipe::all();
        return view('tasks.form_subtask', compact('equipes'));
    }

    public function destroy(Subtask $subtask)
    {
        $parentTask = $subtask->task;

        $subtask->equipes()->detach();
        $subtask->delete();

        if ($parentTask) {
            $parentTask->update([
                'actual_hours' => $parentTask->subtasks()->sum('actual_hours'),
                'estimated_hours' => $parentTask->subtasks()->sum('estimated_hours')
            ]);
        }

        return redirect()->route('tasks.index');
    }
}
