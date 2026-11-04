<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mes projets
        </h2>
    </x-slot>

    <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <a href="{{ route('projects.create') }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Créer un projet</a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @forelse ($projects as $project)
                    <div class="mb-4 p-4 border rounded flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded-full"
                                style="background-color: {{ $project->color }}"></span>
                            <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                        </div>
                        <div class="text-sm text-gray-500 flex gap-4">
                            <span>{{ $project->pivot->role }}</span>
                            <span>{{ $project->tasks_count }} tâche(s)</span>
                        </div>
                    </div>
                @empty
                    <p>Aucun projet trouvé.</p>
                @endforelse

            </div>
        </div>
    </div>
</x-app-layout>