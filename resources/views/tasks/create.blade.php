<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Créer un projet
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('projects.tasks.store', $project) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 font-bold mb-2">Titre de la tâche :</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 font-bold mb-2">Description :</label>
                        <textarea name="description" id="description"
                            value="{{ old('description', $project->description) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="due_date" class="block text-gray-700 font-bold mb-2">Date d'échéance :</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $project->due_date) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                        @error('due_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <select name="priority" id="priority">
                        @foreach (\App\Enums\TaskPriority::cases() as $priority)
                            <option value="{{ $priority->value }}" @selected(old('priority', 2) == $priority->value)>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Créer</button>

                    <a href="{{ route('projects.show', $project) }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                        Annuler
                    </a>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>