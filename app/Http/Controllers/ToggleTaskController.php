<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;

class ToggleTaskController extends Controller
{
    public function __invoke(Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'status' => $task->status === TaskStatus::Done ? TaskStatus::Todo : TaskStatus::Done,
        ]);

        return back();
    }
}