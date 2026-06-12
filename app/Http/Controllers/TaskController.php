<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\Client;
use App\Models\Prospect;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\DailyAssignment;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        $task = Task::createWithLogicAndSubtask($request->validated());

        $task->equipes()->sync($request->equipe_ids ?? []);

        $task->load('equipes');
        foreach ($task->equipes as $equipe) {
            DailyAssignment::incrementTaskCountForToday($equipe->prenom, $task->id, 'task', 'created');
        }

        $redirectRoute = $request->input('redirect_to', 'tasks.index');
        return redirect()->route($redirectRoute);
    }

    public function create(Request $request)
    {
        $equipes = Equipe::all();
        $clients = Client::all();

        $clientCCA = Client::where('nom', 'CCA')->first();

        $prospect = null;
        $existingClient = null;

        if ($request->has('prospect_id')) {
            $prospect = Prospect::with('notes')->find($request->prospect_id);
            if ($prospect) {
                $existingClient = Client::where('nom', $prospect->nom)->first();
            }
        }

        return view('tasks.form_task', compact('clients', 'equipes', 'clientCCA', 'prospect', 'existingClient'));
    }

    public function edit(Task $task)
    {
        $equipes = Equipe::all();
        $clients = Client::all();

        return view('tasks.form_edit_task', compact('task', 'clients', 'equipes'));
    }

    public function editGestion(Task $task)
    {
        return view('gestions.gestion_edit_task', compact('task'));
    }

    public function resetEtat(Task $task)
    {
        $task->update([
            'status' => 'en cours',
            'is_paid' => 0,
            'billing_info' => null,
        ]);

        $task->subtasks()->update([
            'status' => 'en cours',
            'is_paid' => 0,
            'billing_info' => null,
        ]);

        return redirect()->route('gestions.gestion');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->updateWithLogic($request->validated(), $request->only(['add_actual_h', 'add_actual_m']));

        $importantFieldChanged = $task->importantFieldsWereChanged ?? false;

        $changes = $task->equipes()->sync($request->equipe_ids ?? []);
        $newlyAssignedIds = $changes['attached'] ?? [];

        if (!empty($newlyAssignedIds)) {
            $prenoms = \DB::table('equipes')->whereIn('id', $newlyAssignedIds)->pluck('prenom');
            foreach ($prenoms as $prenom) {
                DailyAssignment::incrementTaskCountForToday((string) $prenom, $task->id, 'task', 'updated');
            }
        } elseif ($importantFieldChanged) {
            $task->load('equipes');
            foreach ($task->equipes as $equipe) {
                DailyAssignment::incrementTaskCountForToday((string) $equipe->prenom, $task->id, 'task', 'updated');
            }
        }

        $redirectRoute = $request->input('redirect_to', 'tasks.index');
        return redirect()->route($redirectRoute);
    }

    public function updateGestion(Request $request, Task $task)
    {
        $validated = $request->validate([
            'quote_number' => 'required|string',
            'billing_info' => 'nullable|string',
            'is_paid' => 'required|in:0,1',
        ]);

        $task->update($validated);

        return redirect()->route('gestions.gestion');
    }

    public function duplicate(Request $request, Task $task)
    {
        $newTask = $task->duplicateWithSubtasks();

        $redirectRoute = $request->input('redirect_to', 'tasks.index');
        return redirect()->route($redirectRoute);
    }

    public function destroy(Request $request, Task $task)
    {
        $task->equipes()->detach();
        $task->subtasks()->delete();
        $task->delete();

        $route = $request->input('redirect_to', 'tasks.index');
        return redirect()->route($route);
    }

    public function index(Request $request)
    {
        return view('tasks.index');
    }

    public function indexCCA(Request $request)
    {
        return view('tasks.index_cca');
    }

    public function indexGestion(Request $request)
    {
        return view('gestions.gestion');
    }

    public function indexArchive(Request $request)
    {
        return view('archives.archive');
    }
}
