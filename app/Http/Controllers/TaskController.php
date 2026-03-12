<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Task;
use App\Models\Client;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required',
            'estimated_hours' => 'required|numeric',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string|max:100',
            'billing_info' => 'nullable|string|max:100',

            'equipe_ids' => 'nullable|array',

            'client_id' => 'required_without:new_client_name|nullable|exists:clients,id',
            'new_client_name' => 'required_without:client_id|nullable|string|max:255',
        ]);

        $clientId = $request->client_id;

        if ($request->filled('new_client_name')) {
            $newClient = Client::create([
                'nom' => $request->new_client_name
            ]);
            $clientId = $newClient->id;
        }
        ;

        $task = Task::create([
            'label' => $validated['label'],
            'client_id' => $clientId,
            'estimated_hours' => $validated['estimated_hours'],
            'due_date' => $validated['due_date'],
            'quote_number' => $validated['quote_number'],
            'billing_info' => $validated['billing_info'],
            'actual_hours' => 0,
        ]);

        if ($request->has('equipe_ids')) {
            $task->equipes()->sync($request->equipe_ids);
        }
        ;

        if ($request->has('subtasks')) {
            foreach ($request->subtasks as $subtaskData) {
                $subtask = $task->subtasks()->create([
                    'label' => $subtaskData['label'],
                    'due_date' => $subtaskData['due_date'],
                    'estimated_hours' => $subtaskData['estimated_hours'],
                    'quote_number' => $subtaskData['quote_number'] ?? null,
                    'billing_info' => $subtaskData['billing_info'] ?? null,
                    'actual_hours' => 0,
                ]);

                if (!empty($subtaskData['equipe_ids'])) {
                    $subtask->equipes()->sync($subtaskData['equipe_ids']);
                }
            }
        }

        return redirect()->route('tasks.index');
    }

    public function create()
    {
        $equipes = Equipe::all();
        $clients = Client::all();
        return view('tasks.form_task', compact('clients', 'equipes'));
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $equipes = Equipe::all();
        $clients = Client::all();

        return view('tasks.form_edit_task', compact('task', 'clients', 'equipes'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'estimated_hours' => 'required|numeric',
            'hours_to_add' => 'nullable|numeric',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'equipe_ids' => 'nullable|array',
        ]);

        $newTotalHours = $task->actual_hours + ($request->hours_to_add ?? 0);

        $task->update([
            'label' => $validated['label'],
            'estimated_hours' => $validated['estimated_hours'],
            'due_date' => $validated['due_date'],
            'quote_number' => $validated['quote_number'],
            'billing_info' => $validated['billing_info'],
            'actual_hours' => $newTotalHours,
        ]);

        $task->equipes()->sync($request->equipe_ids ?? []);

        return redirect()->route('tasks.index');
    }

    public function index()
    {
        $tasks = Task::with(['client', 'equipes', 'subtasks.equipes'])->get();

        return view('tasks.index', compact('tasks'));
    }
}
