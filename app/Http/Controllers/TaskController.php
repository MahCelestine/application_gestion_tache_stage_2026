<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Task;
use App\Models\Client;

class TaskController extends Controller
{
    public function store() 
    {
        return redirect()->route('tasks.index');
    }

    public function create() 
    {
        $equipes = Equipe::all();
        $clients = Client::all();
        return view('tasks.form_task', compact('clients', 'equipes'));
    }
    public function index() 
    {
        $tasks= Task::with(['client', 'equipes'])->get();

        return view('tasks.index', compact('tasks'));
    }
}
