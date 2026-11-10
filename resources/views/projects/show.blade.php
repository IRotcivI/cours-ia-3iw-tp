<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
            @endcan

            @can('delete', $project)
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                    onsubmit="return confirm('Supprimer ce projet et toutes ses tâches ?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Supprimer
                    </button>
                </form>
            @endcan

            {{-- Section 1 : infos du projet --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded-full"
                        style="background-color: {{ $project->color }}"></span>
                    <h3 class="text-lg font-semibold">{{ $project->name }}</h3>
                </div>
                <p class="mt-2 text-gray-600">{{ $project->description ?? 'Pas de description.' }}</p>
            </div>

            {{-- Section 2 : membres --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">Membres</h3>
                <ul class="list-disc pl-5">
                    @foreach ($project->members as $member)
                        <li class="text-sm text-gray-500">{{ $member->name }} : {{ $member->pivot->role }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Section 3 : tâches --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">Tâches</h3>
                @foreach ($project->tasks as $task)
                    <div class="mt-2 p-2 border rounded">
                        <h4 class="text-md font-semibold">{{ $task->title }}</h4>
                        <p>{{ $task->description }}</p>
                        <p>Statut : {{ $task->status->label() }}</p>
                        <p>Tags :
                            @foreach ($task->tags as $tag)
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-white rounded"
                                    style="background-color: {{ $tag->color }}" ;>{{ $tag->name }}</span>
                            @endforeach
                        </p>
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $task->priority->color() }}">
                            {{ $task->priority->label() }}
                        </span>
                        <p>Personne assignée : {{ $task->assignee?->name ?? 'Non assignée' }}</p>
                    </div>
                @endforeach

            </div>
            <div class="flex items-center justify-between mb-2">

                @can('create', [App\Models\Task::class, $project])
                    <a href="{{ route('projects.tasks.create', $project) }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Nouvelle tâche
                    </a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>