<?php

namespace App\Http\Controllers;

use App\Ai\Agents\TaskSummarizer;
use App\Services\DailyBriefing;
use Illuminate\Http\Request;
use Throwable;

class BriefingController extends Controller
{
    public function __invoke(Request $request, DailyBriefing $briefing)
    {
        $user = $request->user();
        $tasks = $briefing->tasksFor($user);

        // 1. Rien à résumer : inutile d'appeler le LLM
        if ($tasks->isEmpty()) {
            return back()->with('briefing', "Tu n'as aucune tâche en cours. Profite de ta journée !");
        }

        // 2. Appel au LLM, en prévoyant qu'il puisse échouer
        try {
            $response = TaskSummarizer::make()->prompt(
                $briefing->promptFor($user, $tasks),
                provider: 'local',
            );
        } catch (Throwable $e) {
            report($e);

            return back()->with('briefing_error', "L'assistant n'est pas disponible pour le moment. Réessaie dans un instant.");
        }

        // 3. On renvoie le résumé
        return back()->with('briefing', $response->text);
    }
}