<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\BriefingController;
use App\Http\Controllers\ToggleTaskController;
use App\Http\Controllers\GenerateSubtasksController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)->shallow()->except('index');
    Route::post('tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('tasks.subtasks.store');
    Route::patch('tasks/{task}/toggle', ToggleTaskController::class)->name('tasks.toggle');
    Route::post('tasks/{task}/subtasks/generate', GenerateSubtasksController::class)->name('tasks.subtasks.generate');
    Route::post('/briefing', BriefingController::class)->name('briefing');
});

require __DIR__ . '/auth.php';
