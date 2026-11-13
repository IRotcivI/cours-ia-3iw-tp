<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Mes projets</h2>
                <p class="mt-1 text-sm text-slate-500">Les projets dont tu es membre.</p>
            </div>

            <a href="{{ route('projects.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                + Nouveau projet
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if ($projects->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-12 text-center">
                    <p class="text-4xl">📁</p>
                    <h3 class="mt-4 font-semibold text-slate-900">Aucun projet pour l'instant</h3>
                    <p class="mt-1 text-sm text-slate-500">Crée ton premier projet pour commencer à organiser tes tâches.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        @php
                            $total = $project->tasks_count;
                            $done = $project->done_tasks_count ?? 0;
                            $percent = $total > 0 ? round($done / $total * 100) : 0;

                            $roleClass = match ($project->pivot->role) {
                                'owner' => 'bg-violet-100 text-violet-700',
                                'editor' => 'bg-sky-100 text-sky-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp

                        <a href="{{ route('projects.show', $project) }}"
                            class="group flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-violet-300">

                            <div class="flex items-start justify-between gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl text-lg font-bold text-white"
                                    style="background-color: {{ $project->color }}">
                                    {{ mb_strtoupper(mb_substr($project->name, 0, 1)) }}
                                </span>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClass }}">
                                    {{ ucfirst($project->pivot->role) }}
                                </span>
                            </div>

                            <h3 class="mt-4 font-semibold text-slate-900 group-hover:text-violet-700">
                                {{ $project->name }}
                            </h3>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-500">
                                {{ $project->description ?? 'Pas de description.' }}
                            </p>

                            {{-- Barre de progression --}}
                            <div class="mt-auto pt-6">
                                <div class="flex justify-between text-xs text-slate-500">
                                    <span>{{ $done }} / {{ $total }} tâche(s) terminée(s)</span>
                                    <span class="font-medium text-slate-700">{{ $percent }} %</span>
                                </div>
                                <div class="mt-2 h-1.5 rounded-full bg-slate-100">
                                    <div class="h-1.5 rounded-full"
                                        style="width: {{ $percent }}%; background-color: {{ $project->color }}"></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>