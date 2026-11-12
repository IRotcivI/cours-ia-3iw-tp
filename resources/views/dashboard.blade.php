<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Mon résumé du jour</h3>

                    @if (session('briefing'))
                        <div class="bg-blue-50 border border-blue-200 text-gray-800 p-4 rounded mb-4">
                            {{ session('briefing') }}
                        </div>
                    @endif

                    @if (session('briefing_error'))
                        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded mb-4">
                            {{ session('briefing_error') }}
                        </div>
                    @endif

                    <form action="{{ route('briefing') }}" method="POST"
                        onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Génération en cours…';">
                        @csrf
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                            ✨ Générer mon résumé
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>