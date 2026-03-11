<?php

namespace App\Http\Controllers;

use App\Models\Task;

class TaskController extends Controller
{

    public function index() 
    {
        $tasks= Task::with('client')->get();

        return view('tasks.index', compact('tasks'));
    }
}
