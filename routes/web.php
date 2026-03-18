<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [TaskController::class, 'index']) ->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create']) ->name('tasks.create');
Route::get('/subtasks/create', [SubtaskController::class, 'create']) ->name('subtasks.create');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit']) ->name('tasks.edit');
Route::get('/subtasks/{subtask}/edit', [SubtaskController::class, 'edit'])->name('subtasks.edit');

Route::post('/tasks', [TaskController::class, 'store']) ->name('tasks.store');
Route::post('/subtasks', [SubtaskController::class, 'store']) ->name('subtasks.store');

Route::put('/tasks/{task}', [TaskController::class, 'update']) ->name('tasks.update');
Route::put('/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');

Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');