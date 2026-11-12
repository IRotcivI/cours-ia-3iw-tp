<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Données de démonstration réalistes, en français.
 *
 * Lancement : php artisan migrate:fresh --seeder=DemoSeeder
 * Connexion : alice@demo.fr / password
 */
class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ------------------------------------------------------------------
        // Utilisateurs (mot de passe "password", défini dans UserFactory)
        // ------------------------------------------------------------------
        $alice = User::factory()->create(['name' => 'Alice Martin', 'email' => 'alice@demo.fr']);
        $bob = User::factory()->create(['name' => 'Bob Durand', 'email' => 'bob@demo.fr']);
        $chloe = User::factory()->create(['name' => 'Chloé Bernard', 'email' => 'chloe@demo.fr']);
        $david = User::factory()->create(['name' => 'David Petit', 'email' => 'david@demo.fr']);

        // ------------------------------------------------------------------
        // Projet 1 : Alice est propriétaire (projet principal de la démo)
        // ------------------------------------------------------------------
        $site = Project::factory()->for($alice, 'owner')->create([
            'name' => 'Refonte du site vitrine',
            'description' => 'Nouvelle version du site de l\'agence : design modernisé, formulaire de contact et mise en ligne avant la fin du mois.',
            'color' => '#3b82f6',
        ]);

        $site->members()->attach([
            $alice->id => ['role' => 'owner'],
            $bob->id => ['role' => 'editor'],
            $chloe->id => ['role' => 'viewer'],
        ]);

        $siteTags = $this->tags($site, [
            'urgent' => '#ef4444',
            'client' => '#f59e0b',
            'design' => '#8b5cf6',
            'développement' => '#10b981',
        ]);

        $devis = $this->task($site, $alice, [
            'title' => 'Envoyer le devis au client',
            'description' => 'Devis pour la maintenance annuelle, à valider avant l\'envoi.',
            'assigned_to' => $alice->id,
            'priority' => 3,
            'due_date' => today()->subDays(2),
        ], [$siteTags['client'], $siteTags['urgent']]);

        $this->task($site, $bob, [
            'title' => 'Corriger le bug du formulaire de contact',
            'description' => 'Les messages ne sont pas envoyés quand le champ téléphone est vide.',
            'assigned_to' => $alice->id,
            'priority' => 3,
            'status' => 'in_progress',
            'due_date' => today()->addDay(),
        ], [$siteTags['développement'], $siteTags['urgent']]);

        // Volontairement SANS sous-tâches : idéale pour démontrer « Suggérer des sous-tâches »
        $this->task($site, $alice, [
            'title' => 'Préparer la démo de vendredi',
            'description' => 'Présenter la nouvelle version du site au client : parcours complet, nouveautés et planning de mise en ligne.',
            'assigned_to' => $alice->id,
            'priority' => 3,
            'due_date' => today()->addDays(3),
        ], [$siteTags['client']]);

        $maquette = $this->task($site, $alice, [
            'title' => 'Créer la maquette de la page d\'accueil',
            'assigned_to' => $bob->id,
            'priority' => 2,
            'status' => 'in_progress',
            'due_date' => today()->addDays(5),
        ], [$siteTags['design']]);

        $this->task($site, $bob, [
            'title' => 'Choisir la palette de couleurs',
            'assigned_to' => $bob->id,
            'status' => 'done',
            'completed_at' => now()->subDay(),
        ], parent: $maquette);

        $this->task($site, $bob, [
            'title' => 'Dessiner l\'en-tête et le menu',
            'assigned_to' => $bob->id,
            'status' => 'in_progress',
        ], parent: $maquette);

        $this->task($site, $bob, [
            'title' => 'Faire valider la maquette par le client',
            'assigned_to' => $alice->id,
        ], parent: $maquette);

        $this->task($site, $alice, [
            'title' => 'Mettre en ligne la nouvelle version',
            'priority' => 2,
            'due_date' => today()->addDays(14),
        ], [$siteTags['développement']]);

        $this->task($site, $alice, [
            'title' => 'Rédiger les textes de la page À propos',
            'assigned_to' => $alice->id,
            'priority' => 1,
        ]);

        $this->task($site, $bob, [
            'title' => 'Installer le certificat SSL',
            'assigned_to' => $bob->id,
            'priority' => 2,
            'status' => 'done',
            'completed_at' => now()->subDays(3),
        ], [$siteTags['développement']]);

        $this->comment($devis, $bob, 'Le client attend le devis depuis lundi, il m\'a relancé ce matin.');
        $this->comment($devis, $alice, 'Je m\'en occupe aujourd\'hui en priorité.');
        $this->comment($maquette, $chloe, 'J\'aime beaucoup la première version, le bleu rend bien.');

        // ------------------------------------------------------------------
        // Projet 2 : Bob est propriétaire, Alice est editor
        // ------------------------------------------------------------------
        $mobile = Project::factory()->for($bob, 'owner')->create([
            'name' => 'Application mobile de réservation',
            'description' => 'Application permettant aux clients de réserver un créneau et de payer en ligne.',
            'color' => '#10b981',
        ]);

        $mobile->members()->attach([
            $bob->id => ['role' => 'owner'],
            $alice->id => ['role' => 'editor'],
            $david->id => ['role' => 'viewer'],
        ]);

        $mobileTags = $this->tags($mobile, [
            'backend' => '#6366f1',
            'frontend' => '#ec4899',
            'test' => '#14b8a6',
            'réunion' => '#64748b',
        ]);

        $this->task($mobile, $bob, [
            'title' => 'Écrire les spécifications fonctionnelles',
            'assigned_to' => $alice->id,
            'priority' => 2,
            'due_date' => today()->addDays(4),
        ]);

        $api = $this->task($mobile, $bob, [
            'title' => 'Développer l\'API de réservation',
            'description' => 'Endpoints pour lister les créneaux, réserver et annuler.',
            'assigned_to' => $bob->id,
            'priority' => 3,
            'status' => 'in_progress',
            'due_date' => today()->addDays(7),
        ], [$mobileTags['backend']]);

        $this->task($mobile, $alice, [
            'title' => 'Tester le paiement en ligne',
            'assigned_to' => $alice->id,
            'priority' => 2,
            'due_date' => today()->addDays(10),
        ], [$mobileTags['test'], $mobileTags['frontend']]);

        $this->task($mobile, $bob, [
            'title' => 'Réunion de lancement avec l\'équipe',
            'assigned_to' => $bob->id,
            'priority' => 1,
            'status' => 'done',
            'completed_at' => now()->subDays(5),
        ], [$mobileTags['réunion']]);

        $this->comment($api, $alice, 'Pense à gérer le cas où deux clients réservent le même créneau.');

        // ------------------------------------------------------------------
        // Projet 3 : Chloé est propriétaire, Alice n'est que viewer
        // (pour montrer qu'un viewer ne voit aucun bouton d'action)
        // ------------------------------------------------------------------
        $seminaire = Project::factory()->for($chloe, 'owner')->create([
            'name' => 'Organisation du séminaire d\'équipe',
            'description' => 'Deux jours de séminaire au printemps : lieu, programme et logistique.',
            'color' => '#f59e0b',
        ]);

        $seminaire->members()->attach([
            $chloe->id => ['role' => 'owner'],
            $alice->id => ['role' => 'viewer'],
            $david->id => ['role' => 'editor'],
        ]);

        $this->task($seminaire, $chloe, [
            'title' => 'Réserver la salle de réunion',
            'assigned_to' => $chloe->id,
            'priority' => 3,
            'due_date' => today()->subDay(),
        ]);

        $this->task($seminaire, $chloe, [
            'title' => 'Envoyer les invitations',
            'assigned_to' => $david->id,
            'priority' => 2,
            'due_date' => today()->addDays(6),
        ]);

        $this->task($seminaire, $david, [
            'title' => 'Prévoir le traiteur',
            'assigned_to' => $david->id,
            'priority' => 1,
        ]);
    }

    /**
     * Crée les tags d'un projet et les renvoie, indexés par leur nom.
     */
    private function tags(Project $project, array $tags): Collection
    {
        return collect($tags)->map(
            fn (string $color, string $name) => Tag::factory()->for($project)->create([
                'name' => $name,
                'color' => $color,
            ])
        );
    }

    /**
     * Crée une tâche avec des valeurs par défaut raisonnables.
     */
    private function task(Project $project, User $creator, array $attributes, array $tags = [], ?Task $parent = null): Task
    {
        $task = Task::factory()->for($project)->create(array_merge([
            'user_id' => $creator->id,
            'assigned_to' => null,
            'parent_id' => $parent?->id,
            'description' => null,
            'status' => 'todo',
            'priority' => 2,
            'due_date' => null,
            'completed_at' => null,
        ], $attributes));

        if ($tags !== []) {
            $task->tags()->attach(collect($tags)->pluck('id'));
        }

        return $task;
    }

    /**
     * Ajoute un commentaire sur une tâche.
     */
    private function comment(Task $task, User $author, string $content): void
    {
        Comment::factory()->for($task)->create([
            'user_id' => $author->id,
            'content' => $content,
        ]);
    }
}