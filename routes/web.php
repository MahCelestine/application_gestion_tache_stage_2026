<?php

use App\Http\Controllers\EvolizSyncController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use Illuminate\Support\Facades\Route;
use App\Livewire\TaskFormEvoliz;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::get('/subtasks/create', [SubtaskController::class, 'create'])->name('subtasks.create');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::get('/subtasks/{subtask}/edit', [SubtaskController::class, 'edit'])->name('subtasks.edit');
Route::get('/projet_cca', [TaskController::class, 'indexCCA'])->name('tasks.cca');
Route::get('/gestions', [TaskController::class, 'indexGestion'])->name('gestions.gestion');
Route::get('/gestions/task/{task}/edit', [TaskController::class, 'editGestion'])->name('gestions.task_edit');
Route::get('/gestions/subtask/{subtask}/edit', [SubtaskController::class, 'editGestion'])->name('gestions.subtask_edit');
Route::get('/prospects', [ProspectController::class, 'index'])->name('prospects.prospect');
Route::get('/prospects/create', [ProspectController::class, 'create'])->name('prospects.create');
Route::get('/prospects/{prospect}/edit', [ProspectController::class, 'edit'])->name('prospects.edit');
Route::get('/prospects/{prospect}/transform', [ProspectController::class, 'transform'])->name('prospects.transform');
Route::get('/archives', [TaskController::class, 'indexArchive'])->name('archives.archive');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::post('/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
Route::post('/prospects', [ProspectController::class, 'store'])->name('prospects.store');

Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::put('/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');
Route::put('/gestions/task/{task}', [TaskController::class, 'updateGestion'])->name('gestions.task_update');
Route::put('/gestions/subtask/{subtask}', [SubtaskController::class, 'updateGestion'])->name('gestions.subtask_update');
Route::put('/prospects/{prospect}', [ProspectController::class, 'update'])->name('prospects.update');

Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
Route::delete('/prospects/{prospect}', [ProspectController::class, 'destroy'])->name('prospects.destroy');

Route::patch('/gestions/subtask/{subtask}/reset', [SubtaskController::class, 'resetEtat'])->name('gestions.subtask_reset');
Route::patch('/gestions/task/{task}/reset', [TaskController::class, 'resetEtat'])->name('gestions.task_reset');

Route::get('/sync-evoliz', [EvolizSyncController::class, 'sync'])->name('evoliz.sync');
Route::get('/tasks/import-evoliz', function () {
    return view('tasks.import_evoliz');
})->name('tasks.create-evoliz');