# To-do list augmentée par l'IA

Application Laravel de gestion de projets et de tâches, avec deux fonctionnalités
d'intelligence artificielle qui tournent **en local** grâce à Ollama :

## Prérequis

| Outil | Version |
| --- | --- |
| PHP | 8.3 ou plus |
| Composer | 2 |
| Node.js | 20.19 ou plus |
| Ollama | avec le modèle `qwen2.5:7b` (ou `qwen2.5:3b`) |

La base de données est **SQLite** : aucun serveur à installer.

## Installation

```bash
git clone https://github.com/IRotcivI/cours-ia-3iw-tp.git
cd cours-ia-3iw-tp

composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate
```

### Configurer le modèle

À la fin du fichier `.env`, vérifiez les deux lignes suivantes :

```env
LOCAL_AI_URL=http://localhost:11434/v1
LOCAL_AI_MODEL=qwen2.5:7b
```

Le nom du modèle doit être **exactement** celui affiché par `ollama list`
(remplacez par `qwen2.5:3b` si c'est celui qui est installé). Puis :

```bash
php artisan config:clear
```

## Créer la base avec les données de démonstration

```bash
php artisan migrate:fresh --seeder=DemoSeeder
```

Si Laravel propose de créer le fichier `database/database.sqlite`, répondez `yes`.

Les dates sont calculées par rapport au jour où la commande est lancée : relancez-la
juste avant la démonstration pour que la tâche « Envoyer le devis au client » soit
bien en retard.

## Lancer l'application

```bash
ollama serve
php artisan serve
```

L'application est disponible sur <http://localhost:8000>.

## Comptes de démonstration

Mot de passe pour tous les comptes : **`password`**

| Compte | Rôle dans les projets |
| --- | --- |
| **`alice@demo.fr`** | **compte principal de la démo** : owner de « Refonte du site vitrine », editor de « Application mobile », viewer de « Séminaire » |
| `bob@demo.fr` | owner de « Application mobile », editor de « Refonte du site vitrine » |
| `chloe@demo.fr` | owner de « Séminaire », viewer de « Refonte du site vitrine » |
| `david@demo.fr` | editor de « Séminaire », viewer de « Application mobile » |

## Parcours de démonstration conseillé

1. Se connecter avec **`alice@demo.fr`** / **`password`**.
2. Sur le **Dashboard**, cliquer sur **« ✨ Générer mon résumé »**. La tâche en
   retard est citée en premier. La réponse prend quelques secondes.
3. Ouvrir le projet **« Refonte du site vitrine »**, puis cliquer sur
   **« ✨ Suggérer des sous-tâches »** sous la tâche **« Préparer la démo de
   vendredi »**.
4. Ouvrir le projet **« Organisation du séminaire d'équipe »** : Alice n'y est que
   `viewer`, elle voit tout mais aucun bouton d'action n'est proposé.
