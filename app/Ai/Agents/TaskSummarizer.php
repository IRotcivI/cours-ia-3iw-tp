<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\ProviderTool;
use Stringable;

class TaskSummarizer implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): string
    {
        return "Tu es l'assistant personnel d'une application de gestion de tâches. "
            . "On te donne la date du jour et la liste des tâches d'un utilisateur. "
            . "Tu rédiges un court résumé de sa journée, en 4 à 6 phrases maximum. "
            . "Commence par les tâches en retard, puis les tâches urgentes ou à échéance proche. "
            . "Termine par un conseil sur la tâche à attaquer en premier. "
            . "Tu tutoies l'utilisateur, tu réponds en français, avec un ton encourageant. "
            . "Tu ne mentionnes que les tâches présentes dans la liste, tu n'en inventes jamais.";
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return list<Agent|Tool|ProviderTool>
     */
    public function tools(): iterable
    {
        return [];
    }
}
