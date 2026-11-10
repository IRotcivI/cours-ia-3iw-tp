<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $task->project->roleOf($user) !== null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        return in_array($project->roleOf($user), ['owner', 'editor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return in_array($task->project->roleOf($user), ['owner', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return in_array($task->project->roleOf($user), ['owner', 'editor']);
    }
}
