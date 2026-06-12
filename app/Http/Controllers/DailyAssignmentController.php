<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\DailyAssignment;
use Illuminate\Http\Request;

class DailyAssignmentController extends Controller
{
    public function show(Request $request, $id = null)
    {
        // 1. Récupération de l'assignation journalière active
        $dailyAssignment = $id ? DailyAssignment::findOrFail($id) : auth()->user();

        // Extraction des tableaux d'IDs stockés en JSON dans ton modèle
        $createdTaskIds = $dailyAssignment->created_tasks ?? [];
        $createdSubtaskIds = $dailyAssignment->created_subtasks ?? [];

        $updatedTaskIds = $dailyAssignment->updated_tasks ?? [];
        $updatedSubtaskIds = $dailyAssignment->updated_subtasks ?? [];

        // 2. BLOC 1 : Grandes tâches et sous-tâches CRÉÉES
        // On récupère les grandes tâches enregistrées OU celles qui possèdent une sous-tâche enregistrée
        $createdTasks = Task::query()
            ->whereIn('id', $createdTaskIds)
            ->orWhereHas('subtasks', function ($query) use ($createdSubtaskIds) {
                $query->whereIn('id', $createdSubtaskIds);
            })
            ->with([
                'client',
                'equipes',
                'subtasks' => function ($query) use ($createdSubtaskIds) {
                    $query->whereIn('id', $createdSubtaskIds)
                        ->with('equipes')
                        ->orderBy('id', 'asc'); // Tri standard par ID ou 'due_date'
                }
            ])
            ->filtersSearch($request)
            ->orderBySort($request)
            ->get();

        // 3. BLOC 2 : Grandes tâches et sous-tâches MODIFIÉES
        $updatedTasks = Task::query()
            ->whereIn('id', $updatedTaskIds)
            ->orWhereHas('subtasks', function ($query) use ($updatedSubtaskIds) {
                $query->whereIn('id', $updatedSubtaskIds);
            })
            ->with([
                'client',
                'equipes',
                'subtasks' => function ($query) use ($updatedSubtaskIds) {
                    $query->whereIn('id', $updatedSubtaskIds)
                        ->with('equipes')
                        ->orderBy('id', 'asc'); // Tri standard par ID ou 'due_date'
                }
            ])
            ->filtersSearch($request)
            ->orderBySort($request)
            ->get();

        // 4. Envoi des collections filtrées à la vue (qui reste inchangée et propre)
        return view('daily_assignments.show', compact('dailyAssignment', 'createdTasks', 'updatedTasks'));
    }
}