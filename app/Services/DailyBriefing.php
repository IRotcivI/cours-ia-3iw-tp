<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class DailyBriefing
{
    /**
     * Les tâches à résumer, de la plus urgente à la moins urgente.
     */
    public function tasksFor(User $user): Collection
    {
        return $user->assignedTasks()
            ->where('status', '!=', TaskStatus::Done)
            ->with('project')
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->orderByDesc('priority')
            ->limit(15)
            ->get();
    }

    /**
     * Le prompt envoyé au LLM.
     */
    public function promptFor(User $user, Collection $tasks): string
    {
        $lignes = $tasks->map(fn(Task $task) => $this->formatTask($task));

        $aujourdhui = now()->locale('fr')->isoFormat('dddd D MMMM YYYY');

        return "Aujourd'hui : {$aujourdhui}.\n"
            . "Tâches de {$user->name}, déjà triées de la plus urgente à la moins urgente :\n"
            . $lignes->implode("\n")
            . "\n\nTâche à conseiller en premier : {$tasks->first()->title}.";
    }

    /**
     * Une tâche transformée en une ligne de texte lisible par le LLM.
     */
    private function formatTask(Task $task): string
    {
        $ligne = "- {$task->title} (projet {$task->project->name}), priorité {$task->priority->label()}, ";

        if ($task->due_date === null) {
            return $ligne . "pas d'échéance";
        }

        $ligne .= 'échéance ' . $task->due_date->locale('fr')->isoFormat('dddd D MMMM');

        if ($task->due_date->lt(today())) {
            $ligne .= ', EN RETARD de ' . $task->due_date->diffInDays(today()) . ' jour(s)';
        }

        return $ligne;
    }
}