<div class="mb-4">
    <label for="name" class="block text-gray-700 font-bold mb-2">Nom du projet :</label>
    <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    @error('name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-gray-700 font-bold mb-2">Description :</label>
    <textarea name="description" id="description" value="{{ old('description', $project->description) }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
    @error('description')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="color" class="block text-gray-700 font-bold mb-2">Couleur :</label>
    <input type="color" name="color" id="color" value="{{ old('color', $project->color) }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

    @error('color')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>