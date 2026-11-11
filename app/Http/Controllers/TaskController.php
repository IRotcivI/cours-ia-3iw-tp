<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        Gate::authorize('create', [Task::class, $project]);

        $project->load('members');

        return view('tasks.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request, Project $project)
    {
        Gate::authorize('create', [Task::class, $project]);

        $task = $project->tasks()->make($request->validated());
        $task->creator()->associate($request->user());
        $task->save();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Tâche créée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        Gate::authorize('update', $task);

        $task->load('project.members');

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return redirect()
            ->route('projects.show', $task->project)
            ->with('success', 'Tâche mise à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('projects.show', $task->project)
            ->with('success', 'Tâche supprimée avec succès !');
    }
}
