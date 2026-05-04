<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipe;
use App\Models\Task;
use App\Models\Subtask;
use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Requests\UpdateSubtaskRequest;

class SubtaskController extends Controller
{
    public function store(StoreSubtaskRequest $request)
    {
        $subtask = Subtask::createWithLogic(
            $request->validated(),
            ($request->input('context') === 'cca')
        );

        $subtask->equipes()->sync($request->equipe_ids ?? []);

        $redirect = $request->input('redirect_to', 'task.index');
        return redirect()->route($redirect);
    }

    public function edit(Subtask $subtask)
    {
        $equipes = Equipe::all();
        return view('tasks.form_edit_subtask', compact('subtask', 'equipes'));
    }

    public function editGestion(Subtask $subtask)
    {
        return view('gestions.gestion_edit_subtask', compact('subtask'));
    }

    public function update(UpdateSubtaskRequest $request, Subtask $subtask)
    {
        $subtask->updateLogic(
            $request->validated(),
            $request->only(['estimated_h', 'estimated_m', 'add_actual_h', 'add_actual_m', 'reduce_actual_h', 'reduce_actual_m']),
            ($request->input('context') === 'cca')
        );

        $subtask->equipes()->sync($request->equipe_ids ?? []);

        $redirect = $request->input('redirect_to', 'task.index');
        return redirect()->route($redirect);
    }

    public function updateGestion(Request $request, Subtask $subtask)
    {
        $validated = $request->validate([
            'quote_number' => 'required|string',
            'billing_info' => 'nullable|string',
            'is_paid' => 'required|in:0,1',
        ]);

        $subtask->update($validated);

        return redirect()->route('gestions.gestion');
    }

    public function resetEtat(Subtask $subtask)
    {
        $subtask->update([
            'status' => 'en cours',
            'is_paid' => 0,
            'billing_info' => null,
        ]);

        if ($subtask->task?->status === 'validé') {
            $subtask->task->update([
                'status' => 'en cours',
                'is_paid' => 0,
                'billing_info' => null,
            ]);
        }

        return redirect()->route('gestions.gestion');
    }

    public function create(Request $request)
    {
        $equipes = Equipe::all();
        $parentTask = Task::find($request->task_id);

        return view('tasks.form_subtask', compact('equipes', 'parentTask'));
    }

    public function destroy(Request $request, Subtask $subtask)
    {
        $parentTask = $subtask->task;

        $subtask->equipes()->detach();
        $subtask->delete();

        if ($parentTask) {
            $remainingSubtask = $parentTask->subtasks()->first();

            if ($remainingSubtask) {
                $remainingSubtask->syncParentTask();
            } else {
                $parentTask->update([
                    'actual_hours' => 0,
                    'estimated_hours' => 0,
                    'status' => 'en cours'
                ]);
            }
        }

        $redirect = $request->input('redirect_to', 'task.index');
        return redirect()->route($redirect);
    }
}
