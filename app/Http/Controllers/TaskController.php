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
        $hasSubtasks = $task->subtasks()->count() > 0;

        // 1. VALIDATION DYNAMIQUE
        $rules = [
            'label' => 'required|string|max:255',
            'estimated_hours' => 'required|numeric',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'equipe_ids' => 'nullable|array',
        ];

        if (!$hasSubtasks) {
            $rules['status'] = 'required|in:en cours,validé,bloqué';
            $rules['hours_to_add'] = 'nullable|numeric';
            $rules['reason_description'] = ($request->status === 'bloqué') ? 'required|string' : 'nullable|string';
        }

        $validated = $request->validate($rules);

        if (!$hasSubtasks && $validated['status'] === 'validé') {
            $allSubtaskNonValide = $task->subtasks()->where('status', '!=', 'validé')->count();
            if ($allSubtaskNonValide > 0) {
                return back()->withErrors(['status' => 'Impossible de valider : des sous-tâches sont encore en cours.'])->withInput();
            }
        }

        $updateData = [
            'label' => $validated['label'],
            'estimated_hours' => $validated['estimated_hours'],
            'due_date' => $validated['due_date'],
            'quote_number' => $validated['quote_number'],
            'billing_info' => $validated['billing_info'],
            'client_id' => $validated['client_id'],
        ];

        if (!$hasSubtasks) {
            $updateData['status'] = $validated['status'];
            $updateData['actual_hours'] = $task->actual_hours + ($request->hours_to_add ?? 0);

            if ($validated['status'] === 'bloqué') {
                $currentBlocking = $task->currentBlocking();
                $description = $validated['reason_description'];

                if ($currentBlocking) {
                    $currentBlocking->update(['description' => $description]);
                } else {
                    $task->reasons()->create(['description' => $description, 'is_finish' => false]);
                }
            }

            if ($task->status === 'bloqué' && $validated['status'] !== 'bloqué') {
                $task->reasons()->where('is_finish', false)->update(['is_finish' => true]);
            }
        } else {
            $updateData['actual_hours'] = $task->subtasks()->sum('actual_hours');
        }

        $task->update($updateData);

        $task->equipes()->sync($request->equipe_ids ?? []);

        return redirect()->route('tasks.index')->with('success', 'Tâche mise à jour avec succès.');
    }

    public function destroy(Task $task)
    {
        $task->equipes()->detach();
        $task->subtasks()->delete();
        $task->delete();

        return redirect()->route('tasks.index');
    }

    public function index(Request $request)
    {

        $sortTask = $request->input('sort_task');
        $sortSubtask = $request->input('sort_subtask');
        $sortClient = $request->input('sort_client');
        $filterStatus = $request->input('filter_status');
        $search = $request->input('search');

        $query = Task::select('tasks.*')->with([
            'client',
            'equipes',
            'subtasks' => function ($query) use ($sortSubtask, $filterStatus, $search) {
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('subtasks.label', 'LIKE', "%{$search}%")
                            ->orWhereHas('equipes', function ($equipe) use ($search) {
                                $equipe->where('equipes.prenom', 'LIKE', "%{$search}%")
                                    ->orWhere('equipes.nom', 'LIKE', "%{$search}%");
                            })
                            ->orWhereHas('task', function ($parent) use ($search) {
                                $parent->where('tasks.label', 'LIKE', "%{$search}%")
                                    ->orWhereHas('client', function ($client) use ($search) {
                                        $client->where('clients.nom', 'LIKE', "%{$search}%");
                                    });
                            });
                    });
                }
                if ($filterStatus) {
                    $query->where('subtasks.status', $filterStatus);
                }

                $query->orderBy('due_date', 'asc')->with('equipes');

            }
        ]);


        if ($search) {
            $query->where(function ($q) use ($search, $filterStatus) {
                $q->where('tasks.label', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('clients.nom', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('equipes', function ($equipe) use ($search) {
                        $equipe->where('equipes.prenom', 'LIKE', "%{$search}%")
                            ->orWhere('equipes.nom', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('subtasks', function ($sq) use ($search, $filterStatus) {
                        $sq->where(function ($sub) use ($search) {
                            $sub->where('subtasks.label', 'LIKE', "%{$search}%")
                                ->orWhereHas('equipes', function ($equipe) use ($search) {
                                    $equipe->where('equipes.nom', 'LIKE', "%{$search}%")
                                        ->orWhere('equipes.prenom', 'LIKE', "%{$search}%");
                                });
                        });
                    if ($filterStatus) {
                        $sq->where('subtasks.status', $filterStatus);
                    }

                    });
            });
        }

        if ($filterStatus) {
            $query->where(function ($q) use ($filterStatus, $search) {
                $q->where('tasks.status', $filterStatus)
                ->orWhereHas('subtasks', function($sq) use ($filterStatus, $search) {
                    $sq->where('subtasks.status', $filterStatus);
                    if ($search) {
                        $sq->where(function($sub) use ($search) {
                            $sub->where('subtasks.label', 'LIKE', "%{$search}%")
                            ->orWhereHas('equipes', function($seq) use ($search) {
                                $seq->where('prenom', 'LIKE', "%{$search}%")
                                ->orWhere('nom', 'LIKE', "%{$search}%");
                            });
                        });
                    }
                });
            });
        }

        if ($sortClient) {
            $query->select('tasks.*')
                ->join('clients', 'tasks.client_id', '=', 'clients.id')
                ->orderBy('clients.nom', $sortClient);
        } elseif ($sortTask) {
            $query->orderBy('label', $sortTask);
        } else {
            $query->orderByRaw("FIELD(status, 'bloqué', 'en cours', 'validé')")
                ->orderBy('due_date', 'asc');
        }

        $tasks = $query->get();

        return view('tasks.index', compact('tasks', 'sortTask', 'sortSubtask', 'sortClient', 'filterStatus', 'search'));
    }
}
