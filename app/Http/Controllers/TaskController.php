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
            'estimated_h' => 'required|numeric|min:0',
            'estimated_m' => 'required|numeric|min:0|max:59',
            'due_date' => 'required|date',
            'quote_number' => 'required|string|max:100',
            'billing_info' => 'nullable|string|max:100',
            'equipe_ids' => 'nullable|array',
            'client_id' => 'required_without:new_client_name|nullable|exists:clients,id|prohibits:new_client_name',
            'new_client_name' => 'required_without:client_id|nullable|string|max:255|prohibits:client_id',

            'subtasks' => 'nullable|array',
            'subtasks.*.label' => 'required|string',
            'subtasks.*.due_date' => 'required|date',
            'subtasks.*.estimated_h' => 'required|numeric|min:0',
            'subtasks.*.estimated_m' => 'required|numeric|min:0|max:59',
        ]);

        $clientId = $request->client_id;

        if ($request->filled('new_client_name')) {
            $newClient = Client::create([
                'nom' => $request->new_client_name
            ]);
            $clientId = $newClient->id;
        }
        ;

        $isCCA = ($request->input('context') === 'cca');
        $estimatedHours = $request->input('estimated_h') + ($request->input('estimated_m') / 60);

        $task = Task::create([
            'label' => $validated['label'],
            'client_id' => $clientId,
            'is_paid' => $isCCA,
            'estimated_hours' => $estimatedHours,
            'due_date' => $validated['due_date'],
            'quote_number' => $isCCA ? 'INTERNE' : ($validated['quote_number'] ?? null),
            'billing_info' => $isCCA ? 'OFFERT' : ($validated['billing_info'] ?? null),
            'actual_hours' => 0,
        ]);

        if ($request->has('equipe_ids')) {
            $task->equipes()->sync($request->equipe_ids);
        }
        ;

        if ($request->has('subtasks')) {
            foreach ($request->subtasks as $subtaskData) {
                $subtaskEstimated = (float) $subtaskData['estimated_h'] + ((float) $subtaskData['estimated_m'] / 60);
                $subtask = $task->subtasks()->create([
                    'label' => $subtaskData['label'],
                    'due_date' => $subtaskData['due_date'],
                    'estimated_hours' => $subtaskEstimated,
                    'quote_number' => $subtaskData['quote_number'] ?? null,
                    'billing_info' => $subtaskData['billing_info'] ?? null,
                    'actual_hours' => 0,
                ]);

                if (!empty($subtaskData['equipe_ids'])) {
                    $subtask->equipes()->sync($subtaskData['equipe_ids']);
                }
            }
        }

        $redirectRoute = $request->input('redirect_to', 'tasks.index');
        return redirect()->route($redirectRoute);
    }

    public function create()
    {
        $equipes = Equipe::all();
        $clients = Client::all();

        $clientCCA = Client::where('nom', 'CCA')->first();
        return view('tasks.form_task', compact('clients', 'equipes', 'clientCCA'));
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $equipes = Equipe::all();
        $clients = Client::all();

        return view('tasks.form_edit_task', compact('task', 'clients', 'equipes'));
    }

    public function editGestion($id)
    {
        $task = Task::findOrFail($id);

        return view('gestions.gestion_edit_task', compact('task'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $hasSubtasks = $task->subtasks()->count() > 0;

        $rules = [
            'label' => 'required|string|max:255',
            'due_date' => 'required|date',
            'quote_number' => 'nullable|string',
            'billing_info' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'equipe_ids' => 'nullable|array',
        ];

        if (!$hasSubtasks) {
            $rules['status'] = 'required|in:en cours,validé,bloqué';
            $rules['estimated_h'] = 'required|numeric|min:0';
            $rules['estimated_m'] = 'required|numeric|min:0|max:59';
            $rules['add_actual_h'] = 'nullable|numeric|min:0';
            $rules['add_actual_m'] = 'nullable|numeric|min:0|max:59';
            $rules['reason_description'] = ($request->status === 'bloqué') ? 'required|string' : 'nullable|string';
        }

        $validated = $request->validate($rules);

        if (!$hasSubtasks) {
            $newEstimated = $request->input('estimated_h', 0) + ($request->input('estimated_m', 0) / 60);
            $hoursToAdd = $request->input('add_actual_h', 0) + ($request->input('add_actual_m', 0) / 60);
            $newActual = $task->actual_hours + $hoursToAdd;
        }

        if (!$hasSubtasks && $validated['status'] === 'validé') {
            $allSubtaskNonValide = $task->subtasks()->where('status', '!=', 'validé')->count();
            if ($allSubtaskNonValide > 0) {
                return back()->withErrors(['status' => 'Impossible de valider : des sous-tâches sont encore en cours.'])->withInput();
            }
        }
        if (!$hasSubtasks) {
            $newEstimated = $request->input('estimated_h', 0) + ($request->input('estimated_m', 0) / 60);
            $hoursToAdd = $request->input('add_actual_h', 0) + ($request->input('add_actual_m', 0) / 60);
            $newActual = $task->actual_hours + $hoursToAdd;
        } else {
            $newEstimated = $task->subtasks()->sum('estimated_hours');
            $newActual = $task->subtasks()->sum('actual_hours');
        }

        $updateData = [
            'label' => $validated['label'],
            'estimated_hours' => $newEstimated,
            'actual_hours' => $newActual,
            'due_date' => $validated['due_date'],
            'quote_number' => $subtaskData['quote_number'] ?? null,
            'billing_info' => $subtaskData['billing_info'] ?? null,
            'client_id' => $validated['client_id'],
        ];

        if (!$hasSubtasks) {
            $updateData['status'] = $validated['status'];

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
        }

        $task->update($updateData);

        $task->equipes()->sync($request->equipe_ids ?? []);
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

        $sortTask = $request->input('sort_task');
        $sortSubtask = $request->input('sort_subtask');
        $sortClient = $request->input('sort_client');
        $filterStatus = $request->input('filter_status');
        $search = $request->input('search');

        $query = Task::select('tasks.*');

        $query->whereHas('client', function ($q) {
            $q->where('nom', '!=', 'CCA');
        });

        $query->with([
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
                                        $client->where('clients.nom', 'LIKE', "%{$search}%")
                                            ->where('clients.nom', '!=', 'CCA');
                                    });
                            });
                    });
                }
                if ($filterStatus) {
                    $query->where('subtasks.status', $filterStatus);
                }

                if ($sortSubtask) {
                    $query->orderBy('label', $sortSubtask);
                } else {
                    $query->orderByRaw("FIELD(status, 'bloqué', 'en cours', 'validé')")
                        ->orderBy('due_date', 'asc');
                }

                $query->with('equipes');

            }
        ]);


        if ($search) {
            $query->where(function ($q) use ($search, $filterStatus) {
                $q->where('tasks.label', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('clients.nom', 'LIKE', "%{$search}%")
                            ->where('clients.nom', '!=', 'CCA');
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
                    ->orWhereHas('subtasks', function ($sq) use ($filterStatus, $search) {
                        $sq->where('subtasks.status', $filterStatus);
                        if ($search) {
                            $sq->where(function ($sub) use ($search) {
                                $sub->where('subtasks.label', 'LIKE', "%{$search}%")
                                    ->orWhereHas('equipes', function ($seq) use ($search) {
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

    public function indexCCA(Request $request)
    {

        $sortTask = $request->input('sort_task');
        $sortSubtask = $request->input('sort_subtask');
        $sortClient = $request->input('sort_client');
        $filterStatus = $request->input('filter_status');
        $search = $request->input('search');

        $query = Task::whereHas('client', function ($q) {
            $q->where('nom', 'CCA');
        })->with([
                    'client',
                    'equipes',
                    'subtasks' => function ($query) use ($sortSubtask, $filterStatus, $search) {
                        if ($search) {
                            $query->where(function ($q) use ($search) {
                                $q->where('subtasks.label', 'LIKE', "%{$search}%")
                                    ->orWhereHas('equipes', function ($equipe) use ($search) {
                                        $equipe->where('equipes.prenom', 'LIKE', "%{$search}%")
                                            ->orWhere('equipes.nom', 'LIKE', "%{$search}%");
                                    });
                            });
                        }
                        if ($filterStatus) {
                            $query->where('subtasks.status', $filterStatus);
                        }

                        if ($sortSubtask) {
                            $query->orderBy('label', $sortSubtask);
                        } else {
                            $query->orderByRaw("FIELD(status, 'bloqué', 'en cours', 'validé')")
                                ->orderBy('due_date', 'asc');
                        }

                        $query->with('equipes');

                    }
                ]);


        if ($search) {
            $query->where(function ($q) use ($search, $filterStatus) {
                $q->where('tasks.label', 'LIKE', "%{$search}%")
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
                    ->orWhereHas('subtasks', function ($sq) use ($filterStatus, $search) {
                        $sq->where('subtasks.status', $filterStatus);
                        if ($search) {
                            $sq->where(function ($sub) use ($search) {
                                $sub->where('subtasks.label', 'LIKE', "%{$search}%")
                                    ->orWhereHas('equipes', function ($seq) use ($search) {
                                        $seq->where('prenom', 'LIKE', "%{$search}%")
                                            ->orWhere('nom', 'LIKE', "%{$search}%");
                                    });
                            });
                        }
                    });
            });
        }

        if ($sortTask) {
            $query->orderBy('label', $sortTask);
        } else {
            $query->orderByRaw("FIELD(status, 'bloqué', 'en cours', 'validé')")
                ->orderBy('due_date', 'asc');
        }

        $tasks = $query->get();

        return view('tasks.index_cca', compact('tasks', 'sortTask', 'sortSubtask', 'sortClient', 'filterStatus', 'search'));
    }

    public function indexGestion(Request $request)
    {
        $sortTask = $request->input('sort_task');
        $sortSubtask = $request->input('sort_subtask');
        $sortClient = $request->input('sort_client');
        $filterPayement = $request->input('filter_payment');
        $search = $request->input('search');

        $priorityRaw = "CASE
        WHEN billing_info IS NULL THEN 1
        WHEN is_paid = 0 THEN 2
        ELSE 3
        END";

        $query = Task::whereHas('client', fn($q) => $q->where('nom', '!=', 'CCA'));

        $query->where(function ($q) {
            $q->where('status', 'validé')
                ->orWhereHas('subtasks', fn($sq) => $sq->where('status', 'validé'));
        });

        $query->where(function ($q) {
            $q->where('is_paid', false)
                ->orWhereHas('subtasks', function ($sq) {
                    $sq->where('is_paid', false);
                });
        });

        if ($filterPayement) {
            $query->where(function ($q) use ($filterPayement) {
                if ($filterPayement === 'a_facturer') {
                    $q->whereNull('billing_info')
                        ->orWhereHas('subtasks', fn($sq) => $sq->whereNull('billing_info'));
                } elseif ($filterPayement === 'non_paye') {
                    $q->where(fn($sub) => $sub->whereNotNull('billing_info')->where('is_paid', false))
                        ->orWhereHas('subtasks', fn($sq) => $sq->whereNotNull('billing_info')->where('is_paid', false));
                } elseif ($filterPayement === 'paye') {
                    $q->where('is_paid', true)
                        ->orWhereHas('subtasks', fn($sq) => $sq->where('is_paid', true));
                }
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('label', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', fn($cq) => $cq->where('nom', 'LIKE', "%{$search}%"))
                    ->orWhereHas('subtasks', fn($sq) => $sq->where('label', 'LIKE', "%{$search}%"));
            });
        }

        $query->with([
            'client',
            'subtasks' => function ($q) use ($search, $sortSubtask, $filterPayement, $priorityRaw) {
                $q->where('status', 'validé');

                if($search) {
                    $q->where('label', 'LIKE', "%{$search}%");
                }

                if ($filterPayement === 'paye') {
                    $q->where('is_paid', true);
                } elseif ($filterPayement === 'non_paye') {
                    $q->where('is_paid', false)->whereNotNull('billing_info');
                } elseif ($filterPayement === 'a_facturer') {
                    $q->whereNull('billing_info');
                }

                $q->orderByRaw($priorityRaw)->orderBy('due_date', 'asc');

                if ($sortSubtask) {
                    $q->orderBy('label', $sortSubtask);
                }
            }
        ]);

        if ($sortClient) {
            $query->join('clients', 'tasks.client_id', '=', 'clients.id')
                ->select('tasks.*')
                ->orderBy('clients.nom', $sortClient);
        } elseif ($sortTask) {
            $query->orderBy('label', $sortTask);
        } else {
            $query->orderByRaw($priorityRaw)
            ->orderBy('due_date', 'asc');
        }

        $tasks = $query->get();

        return view('gestions.gestion', compact('tasks', 'sortTask', 'sortSubtask', 'sortClient', 'search'));
    }

    public function indexArchive(Request $request) 
    {
        return view('archives.archive', compact('tasks', 'sortTask', 'sortSubtask', 'sortClient', 'search'));
    }
}
