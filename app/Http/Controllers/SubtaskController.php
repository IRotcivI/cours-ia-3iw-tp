<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Task;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        Gate::authorize('create', [Task::class, $task->project]);

        abort_if($task->parent_id !== null, 422, 'Une sous-tâche ne peut pas avoir de sous-tâches.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $subtask = $task->subtasks()->make([
            ...$validated,
            'project_id' => $task->project_id,
        ]);
        $subtask->creator()->associate($request->user());
        $subtask->save();

        return redirect()
            ->route('projects.show', $task->project)
            ->with('success', 'Sous-tâche créée avec succès !');
    }
}
