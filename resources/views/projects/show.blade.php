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
                        @can('update', $task)
                            <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-lg" title="Changer le statut">
                                    {{ $task->status === App\Enums\TaskStatus::Done ? '☑' : '☐' }}
                                </button>
                            </form>
                        @endcan
                        <h4
                            class="font-semibold {{ $task->status === App\Enums\TaskStatus::Done ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </h4>
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

                        @can('update', $task)
                            <a href="{{ route('tasks.edit', $task) }}"
                                class="text-blue-600 hover:underline text-sm">Modifier</a>
                        @endcan

                        @can('delete', $task)
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline"
                                onsubmit="return confirm('Supprimer cette tâche ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Supprimer</button>
                            </form>
                        @endcan

                        @can('create', [App\Models\Task::class, $project])
                            <form action="{{ route('tasks.subtasks.store', $task) }}" method="POST"
                                class="mt-2 ml-4 flex gap-2">
                                @csrf
                                <input type="text" name="title" placeholder="Nouvelle sous-tâche…"
                                    class="border rounded px-2 py-1 text-sm flex-1">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded">
                                    Ajouter
                                </button>
                            </form>
                        @endcan
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