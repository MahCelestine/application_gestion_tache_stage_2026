<x-layout>
    <x-slot:searchBar>
        <livewire:search-bar placeholder="Client, tâche ou assignation..." />
    </x-slot:searchBar>

    <x-slot:ajoutTache>
        <a href="{{ route('tasks.create') }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">
            + Ajouter une
            grande tâche</a>
    </x-slot:ajoutTache>

    <livewire:task-filters name="filter_status" label="Filtrer par état" :options="[
        'en cours' => 'En cours',
        'bloqué' => 'Bloqués',
        'attente BAT' => 'En attente de BAT',
        'BAT ok' => 'BAT OK',
        'validé' => 'Validés'
    ]" :current="request('filter_status', '')" />

    <div class="flex items-center gap-3">
        <a href="{{ route('evoliz.sync') }}"
            class="btn-fill-animation group relative flex items-center text-sm z-10 text-blue-500 border border-blue-500 rounded-2xl py-1.5 px-4 overflow-hidden transition-colors duration-300 hover:text-white">
            <span class="relative z-20 font-medium">Synchro Evoliz</span>
        </a>

        @if(session('info') && !session('pending_evoliz_lines'))
            <div
                class="flex items-center text-green-600 font-bold bg-green-100 px-3 py-1.5 rounded-full border border-green-200 animate-fade-in text-xs">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                À jour
            </div>
        @endif
    </div>

    <livewire:task-list :isCca="false" />

    <script>
        document.addEventListener("livewire:navigated", () => {
            ScrollTrigger.refresh();
        });
    </script>

</x-layout>