<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipe;
use App\Models\Subtask;

class SubtaskController extends Controller
{
    public function store(Request $request) 
    {
        $validated= $request->validate ([
            'task_id' => 'required',
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
            'actual_hours' =>0,
        ]);

        if ($request->has('equipe_ids')) 
        {
            $subtask->equipes()->sync($request->equipe_ids);
        };

        return redirect()->route('tasks.index');
    }

    public function create() 
    {
        $equipes = Equipe::all();
        return view('tasks.form_subtask', compact('equipes'));
    }
}
