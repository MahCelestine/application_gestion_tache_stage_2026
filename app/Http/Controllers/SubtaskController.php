<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipe;
use App\Models\Task;
use App\Models\Subtask;
use Carbon\Carbon;

class SubtaskController extends Controller
{
    public function store(Request $request)
    {
        $parentTask = Task::findOrFail($request->task_id);

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'label' => 'required|string|max:255',
            'due_date' => 'required|date',
            'estimated_h' => 'required|numeric|min:0',
            'estimated_m' => 'required|numeric|min:0|max:59',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'equipe_ids' => 'nullable|array',
        ]);

        $isCCA = ($request->input('context') === 'cca');
        $estimated = (float) $request->input('estimated_h') + ((float) $request->input('estimated_m') / 60);

        $subtaskDate = Carbon::parse($validated['due_date']);
        $parentDate = Carbon::parse($parentTask->due_date);

        if ($subtaskDate->gt($parentDate)) {
            $parentTask->update(['due_date' => $validated['due_date']]);
        }

        $subtask = Subtask::create([
            'task_id' => $validated['task_id'],
            'label' => $validated['label'],
            'due_date' => $validated['due_date'],
            'estimated_hours' => $estimated,
            'quote_number' => $isCCA ? 'INTERNE' : ($validated['quote_number'] ?? null),
            'billing_info' => $isCCA ? 'OFFERT' : ($validated['billing_info'] ?? null),
            'actual_hours' => 0,
        ]);

        if ($request->has('equipe_ids')) {
            $subtask->equipes()->sync($request->equipe_ids);
        }

        if ($parentTask) {
            $parentTask->update([
                'estimated_hours' => $parentTask->subtasks()->sum('estimated_hours')
            ]);
        }

        $redirect = $request->input('redirect_to', 'task.index');
        return redirect()->route($redirect);
    }

    public function edit($id)
    {
        $subtask = Subtask::findOrFail($id);
        $equipes = Equipe::all();

        return view('tasks.form_edit_subtask', compact('subtask', 'equipes'));
    }

    public function editGestion($id)
    {
        $subtask = Subtask::findOrFail($id);

        return view('gestions.gestion_edit_subtask', compact('subtask'));
    }

    public function update(Request $request, $id)
    {
        $subtask = Subtask::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'status' => 'required|in:en cours,validé,bloqué',
            'reason_description' => 'nullable|string|required_if:status,bloqué',
            'estimated_h' => 'required|numeric|min:0',
            'estimated_m' => 'required|numeric|min:0|max:59',
            'add_actual_h' => 'nullable|numeric|min:0',
            'add_actual_m' => 'nullable|numeric|min:0|max:59',
            'reduce_actual_h' => 'nullable|numeric|min:0',
            'reduce_actual_m' => 'nullable|numeric|min:0|max:59',
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
        $currentActualHours = $subtask->actual_hours;
        $newEstimated = $validated['estimated_h'] + ($validated['estimated_m'] / 60);
        $decimalToAdd = ($request->input('add_actual_h', 0)) + ($request->input('add_actual_m', 0) / 60);
        $decimalToReduce = ($request->input('reduce_actual_h', 0)) + ($request->input('reduce_actual_m', 0) / 60);
        $newActualTotal = $currentActualHours + $decimalToAdd - $decimalToReduce;

        $subtask->actual_hours = max(0, $newActualTotal);
        $subtask->save();

        $isCCA = ($request->input('context') === 'cca');


        $subtask->update([
            'status' => $validated['status'],
            'label' => $validated['label'],
            'estimated_hours' => $newEstimated,
            'actual_hours' => $newActualTotal,
            'due_date' => $validated['due_date'],
            'quote_number' => $isCCA ? 'INTERNE' : $validated['quote_number'] ?? null,
            'billing_info' => $isCCA ? 'OFFERT' : $validated['billing_info'] ?? null,
        ]);

        $parentTask = $subtask->task;
        $maxSubtaskDate = $parentTask->subtasks()->max('due_date');

        $parentTask->update([
            'actual_hours' => $parentTask->subtasks()->sum('actual_hours'),
            'estimated_hours' => $parentTask->subtasks()->sum('estimated_hours')
        ]);

        if ($maxSubtaskDate) {
            $parentTask->update(['due_date' => $maxSubtaskDate]);
        }

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

        $redirect = $request->input('redirect_to', 'task.index');
        return redirect()->route($redirect);
    }

    public function updateGestion(Request $request, $id) 
    {
        $subtask = Subtask::findOrFail($id);

        $validated = $request->validate([
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'is_paid' => 'required|in:0,1',
        ]);

        $subtask->update($validated);

        return redirect()->route('gestions.gestion');
    }

    public function create()
    {
        $equipes = Equipe::all();
        return view('tasks.form_subtask', compact('equipes'));
    }

    public function destroy(Request $request, Subtask $subtask)
    {
        $parentTask = $subtask->task;

        $subtask->equipes()->detach();
        $subtask->delete();

        if ($parentTask) {
            $parentTask->update([
                'actual_hours' => $parentTask->subtasks()->sum('actual_hours'),
                'estimated_hours' => $parentTask->subtasks()->sum('estimated_hours')
            ]);

            $maxSubtaskDate = $parentTask->subtasks()->max('due_date');

            if ($maxSubtaskDate) {
                $parentTask->update(['due_date' => $maxSubtaskDate]);
            }
        }

        $redirect = $request->input('redirect_to', 'task.index');
        return redirect()->route($redirect);
    }
}
