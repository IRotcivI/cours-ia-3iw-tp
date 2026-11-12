<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SubtaskGenerator;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class GenerateSubtasksController extends Controller
{
    public function __invoke(Request $request, Task $task)
    {
        // 1. Les mêmes vérifications que pour un ajout manuel
        Gate::authorize('create', [Task::class, $task->project]);
        abort_if($task->parent_id !== null, 422, 'Une sous-tâche ne peut pas avoir de sous-tâches.');

        // 2. L'appel au LLM, protégé
        try {
            $response = SubtaskGenerator::make()->prompt($this->promptFor($task), provider: 'local');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', "L'assistant n'est pas disponible pour le moment.");
        }

        // 3. Valider ce que renvoie le LLM
        $existing = $task->subtasks->pluck('title')->map(fn($title) => mb_strtolower($title));

        $titles = collect($response['subtasks'] ?? [])
            ->filter(fn($title) => is_string($title))
            ->map(fn($title) => trim($title))
            ->filter(fn($title) => $title !== '' && mb_strlen($title) <= 255)
            ->unique()
            ->reject(fn($title) => $existing->contains(mb_strtolower($title)))
            ->take(5);

        if ($titles->isEmpty()) {
            return back()->with('error', "L'assistant n'a pas trouvé de sous-tâche à proposer.");
        }

        // 4. Créer les sous-tâches, toutes ou aucune
        DB::transaction(function () use ($titles, $task, $request) {
            foreach ($titles as $title) {
                $task->addSubtask($title, $request->user());
            }
        });

        return back()->with('success', $titles->count() . ' sous-tâche(s) ajoutée(s) par l\'assistant.');
    }

    private function promptFor(Task $task): string
    {
        $existing = $task->subtasks->pluck('title')->implode(', ') ?: 'aucune';

        return "Tâche : {$task->title}.\n"
            . 'Description : ' . ($task->description ?? 'aucune') . ".\n"
            . "Sous-tâches existantes : {$existing}.";
    }
}