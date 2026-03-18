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

        // On n'ajoute la validation du status que s'il n'y a PAS de sous-tâches
        if (!$hasSubtasks) {
            $rules['status'] = 'required|in:en cours,validé,bloqué';
            $rules['hours_to_add'] = 'nullable|numeric';
            $rules['reason_description'] = ($request->status === 'bloqué') ? 'required|string' : 'nullable|string';
        }

        $validated = $request->validate($rules);

        // 2. SÉCURITÉ : Empêcher la validation manuelle si des sous-tâches sont encore actives
        if (!$hasSubtasks && $validated['status'] === 'validé') {
            // (Cette sécurité est plus utile si vous aviez des sous-tâches, 
            // mais on la garde par cohérence avec votre logique précédente)
            $allSubtaskNonValide = $task->subtasks()->where('status', '!=', 'validé')->count();
            if ($allSubtaskNonValide > 0) {
                return back()->withErrors(['status' => 'Impossible de valider : des sous-tâches sont encore en cours.'])->withInput();
            }
        }

        // 3. PRÉPARATION DES DONNÉES DE BASE
        $updateData = [
            'label' => $validated['label'],
            'estimated_hours' => $validated['estimated_hours'],
            'due_date' => $validated['due_date'],
            'quote_number' => $validated['quote_number'],
            'billing_info' => $validated['billing_info'],
            'client_id' => $validated['client_id'],
        ];

        // 4. LOGIQUE SELON LA PRÉSENCE DE SOUS-TÂCHES
        if (!$hasSubtasks) {
            // --- CAS SANS SOUS-TÂCHES (Gestion Manuelle) ---
            $updateData['status'] = $validated['status'];
            $updateData['actual_hours'] = $task->actual_hours + ($request->hours_to_add ?? 0);

            // Gestion des raisons de blocage
            if ($validated['status'] === 'bloqué') {
                $currentBlocking = $task->currentBlocking();
                $description = $validated['reason_description'];

                if ($currentBlocking) {
                    $currentBlocking->update(['description' => $description]);
                } else {
                    $task->reasons()->create(['description' => $description, 'is_finish' => false]);
                }
            }

            // Si on passe de bloqué à autre chose, on ferme la raison
            if ($task->status === 'bloqué' && $validated['status'] !== 'bloqué') {
                $task->reasons()->where('is_finish', false)->update(['is_finish' => true]);
            }
        } else {
            // --- CAS AVEC SOUS-TÂCHES (Gestion Automatique) ---
            // On ne touche pas au 'status', il reste celui calculé par les sous-tâches
            // On recalcule le temps total basé sur les enfants
            $updateData['actual_hours'] = $task->subtasks()->sum('actual_hours');
        }

        // 5. SAUVEGARDE FINALE
        $task->update($updateData);

        // Synchronisation des équipes (Table Pivot)
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

    public function index()
    {
        $tasks = Task::with(['client', 'equipes', 'subtasks' => function ($query) {
                $query->orderByRaw("FIELD(status, 'bloqué', 'en cours', 'validé')")
                    ->orderBy('due_date', 'asc')
                    ->with('equipes');
            }])
            ->orderByRaw("FIELD(status, 'bloqué', 'en cours', 'validé')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('tasks.index', compact('tasks'));
    }
}
