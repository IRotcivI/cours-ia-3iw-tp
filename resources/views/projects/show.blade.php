@php
    $myRole = $project->roleOf(Auth::user());
    $roleClasses = [
        'owner' => 'bg-violet-100 text-violet-700',
        'editor' => 'bg-sky-100 text-sky-700',
        'viewer' => 'bg-slate-100 text-slate-600',
    ];
    $totalTasks = $project->tasks->count();
    $doneTasks = $project->tasks->where('status', App\Enums\TaskStatus::Done)->count();
    $percent = $totalTasks > 0 ? round($doneTasks / $totalTasks * 100) : 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-xl font-bold text-white"
                    style="background-color: {{ $project->color }}">
                    {{ mb_strtoupper(mb_substr($project->name, 0, 1)) }}
                </span>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-slate-900">{{ $project->name }}</h2>
                        @if ($myRole)
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClasses[$myRole] ?? '' }}">
                                {{ ucfirst($myRole) }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $project->description ?? 'Pas de description.' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}"
                        class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-50">
                        Modifier
                    </a>
                @endcan

                @can('delete', $project)
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                        onsubmit="return confirm('Supprimer ce projet et toutes ses tâches ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-red-600 ring-1 ring-red-200 transition hover:bg-red-50">
                            Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">

            {{-- ===================== Colonne principale : les tâches ===================== --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-slate-900">Tâches</h3>

                        @can('create', [App\Models\Task::class, $project])
                            <a href="{{ route('projects.tasks.create', $project) }}"
                                class="inline-flex items-center gap-1 rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                                + Nouvelle tâche
                            </a>
                        @endcan
                    </div>

                    <ul class="mt-6 space-y-4">
                        @forelse ($project->tasks as $task)
                            @php
                                $isDone = $task->status === App\Enums\TaskStatus::Done;
                                $isLate = ! $isDone && $task->due_date && $task->due_date->lt(today());
                                $statusClass = match ($task->status) {
                                    App\Enums\TaskStatus::Done => 'bg-emerald-50 text-emerald-700',
                                    App\Enums\TaskStatus::InProgress => 'bg-amber-50 text-amber-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp

                            <li class="rounded-xl border border-slate-200 p-4 transition hover:border-violet-200 hover:shadow-sm">
                                <div class="flex items-start gap-3">

                                    {{-- Case à cocher --}}
                                    @can('update', $task)
                                        <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="pt-0.5">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Changer le statut"
                                                class="flex h-5 w-5 items-center justify-center rounded-md border-2 transition {{ $isDone ? 'border-violet-600 bg-violet-600 text-white' : 'border-slate-300 hover:border-violet-500' }}">
                                                @if ($isDone)
                                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @else
                                        <span class="mt-0.5 h-5 w-5 shrink-0 rounded-md border-2 {{ $isDone ? 'border-violet-600 bg-violet-600' : 'border-slate-200' }}"></span>
                                    @endcan

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-medium {{ $isDone ? 'text-slate-400 line-through' : 'text-slate-900' }}">
                                                {{ $task->title }}
                                            </h4>
                                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $task->priority->color() }}">
                                                {{ $task->priority->label() }}
                                            </span>
                                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">
                                                {{ $task->status->label() }}
                                            </span>
                                        </div>

                                        @if ($task->description)
                                            <p class="mt-1 text-sm text-slate-500">{{ $task->description }}</p>
                                        @endif

                                        {{-- Échéance, personne assignée, tags --}}
                                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500">
                                            @if ($task->due_date)
                                                <span class="{{ $isLate ? 'font-semibold text-red-600' : '' }}">
                                                    📅 {{ $task->due_date->format('d/m/Y') }}{{ $isLate ? ' · en retard' : '' }}
                                                </span>
                                            @endif

                                            <span class="flex items-center gap-1.5">
                                                @if ($task->assignee)
                                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-violet-100 text-[10px] font-semibold text-violet-700">
                                                        {{ mb_strtoupper(mb_substr($task->assignee->name, 0, 1)) }}
                                                    </span>
                                                    {{ $task->assignee->name }}
                                                @else
                                                    Non assignée
                                                @endif
                                            </span>

                                            @foreach ($task->tags as $tag)
                                                <span class="rounded-md px-2 py-0.5 font-medium text-white"
                                                    style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex shrink-0 items-center gap-3 text-xs">
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-slate-500 transition hover:text-violet-700">Modifier</a>
                                        @endcan
                                        @can('delete', $task)
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                                onsubmit="return confirm('Supprimer cette tâche ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-500 transition hover:text-red-600">Supprimer</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>

                                {{-- Sous-tâches --}}
                                @if ($task->subtasks->isNotEmpty())
                                    <ul class="ml-8 mt-4 space-y-2 border-l-2 border-slate-100 pl-4">
                                        @foreach ($task->subtasks as $subtask)
                                            @php $subDone = $subtask->status === App\Enums\TaskStatus::Done; @endphp
                                            <li class="flex items-center gap-2 text-sm">
                                                @can('update', $subtask)
                                                    <form action="{{ route('tasks.toggle', $subtask) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" title="Changer le statut"
                                                            class="flex h-4 w-4 items-center justify-center rounded border-2 transition {{ $subDone ? 'border-violet-600 bg-violet-600 text-white' : 'border-slate-300 hover:border-violet-500' }}">
                                                            @if ($subDone)
                                                                <svg class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endcan
                                                <span class="{{ $subDone ? 'text-slate-400 line-through' : 'text-slate-700' }}">{{ $subtask->title }}</span>
                                                @if ($subtask->assignee)
                                                    <span class="text-xs text-slate-400">· {{ $subtask->assignee->name }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                {{-- Ajout manuel et suggestion IA --}}
                                @can('create', [App\Models\Task::class, $project])
                                    <div class="ml-8 mt-4 flex flex-wrap items-center gap-2">
                                        <form action="{{ route('tasks.subtasks.store', $task) }}" method="POST" class="flex min-w-[14rem] flex-1 gap-2">
                                            @csrf
                                            <input type="text" name="title" placeholder="Ajouter une sous-tâche…"
                                                class="flex-1 rounded-lg border-slate-200 px-3 py-1.5 text-sm focus:border-violet-500 focus:ring-violet-500">
                                            <button type="submit"
                                                class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200">
                                                Ajouter
                                            </button>
                                        </form>

                                        <form action="{{ route('tasks.subtasks.generate', $task) }}" method="POST"
                                            onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Réflexion en cours…';">
                                            @csrf
                                            <button type="submit"
                                                class="rounded-lg bg-violet-50 px-3 py-1.5 text-sm font-medium text-violet-700 ring-1 ring-violet-200 transition hover:bg-violet-100 disabled:cursor-wait disabled:opacity-60">
                                                ✨ Suggérer des sous-tâches
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            </li>
                        @empty
                            <li class="rounded-xl border-2 border-dashed border-slate-200 p-10 text-center text-sm text-slate-500">
                                Aucune tâche pour l'instant.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- ===================== Colonne de droite ===================== --}}
            <div class="space-y-6">

                {{-- Progression --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Progression</h3>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $percent }} %</p>
                    <p class="text-sm text-slate-500">{{ $doneTasks }} tâche(s) terminée(s) sur {{ $totalTasks }}</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full" style="width: {{ $percent }}%; background-color: {{ $project->color }}"></div>
                    </div>
                </div>

                {{-- Membres --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Membres</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($project->members as $member)
                            <li class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-100 text-sm font-semibold text-violet-700">
                                        {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </span>
                                    <span class="text-sm font-medium text-slate-800">{{ $member->name }}</span>
                                </div>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClasses[$member->pivot->role] ?? '' }}">
                                    {{ ucfirst($member->pivot->role) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>