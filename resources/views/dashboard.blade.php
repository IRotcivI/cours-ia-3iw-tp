<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}</p>
            <h2 class="mt-1 text-2xl font-bold text-slate-900">
                Bonjour {{ \Illuminate\Support\Str::before(Auth::user()->name, ' ') }} 👋
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Carte du résumé IA --}}
            <div class="relative overflow-hidden rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                {{-- Halo décoratif --}}
                <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-violet-100 blur-3xl"></div>

                <div class="relative">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-600 text-xl text-white shadow-sm">✨</span>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Mon résumé du jour</h3>
                            <p class="text-sm text-slate-500">Ton assistant IA fait le point sur tes tâches.</p>
                        </div>
                    </div>

                    @if (session('briefing'))
                        <div class="mt-6 rounded-xl border border-violet-100 bg-violet-50 p-5 leading-relaxed text-slate-700">
                            {{ session('briefing') }}
                        </div>
                    @elseif (session('briefing_error'))
                        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            {{ session('briefing_error') }}
                        </div>
                    @else
                        <p class="mt-6 text-slate-500">
                            Clique sur le bouton pour savoir par quoi commencer aujourd'hui.
                        </p>
                    @endif

                    <form action="{{ route('briefing') }}" method="POST" class="mt-6"
                        onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Génération en cours…';">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 disabled:cursor-wait disabled:opacity-60">
                            ✨ Générer mon résumé
                        </button>
                    </form>
                </div>
            </div>

            {{-- Raccourci vers les projets --}}
            <a href="{{ route('projects.index') }}"
                class="group flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:ring-violet-300">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl">📁</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">Mes projets</h3>
                        <p class="text-sm text-slate-500">Retrouve tes projets, tes tâches et ton équipe.</p>
                    </div>
                </div>
                <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-violet-600">→</span>
            </a>

        </div>
    </div>
</x-app-layout>