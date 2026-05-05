<x-layout>
    <x-slot:ajoutTache>
        <a href="{{ route('tasks.create', ['context' => 'cca']) }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]"> + Ajouter une
            grande tâche</a>
    </x-slot:ajoutTache>
    <livewire:task-filters name="filter_status" label="Filtrer par état" :options="[
        'en cours' => 'En cours',
        'bloqué' => 'Bloqués',
        'attente BAT' => 'En attente de BAT',
        'validé' => 'Validés'
    ]" :current="request('filter_status', '')" />
    <x-slot:searchBar>
        <livewire:search-bar placeholder="Client, tâche ou assignation..." />
    </x-slot:searchBar>

    <livewire:task-list :isCca="true" />

    <script>
        document.addEventListener("livewire:navigated", () => {
            ScrollTrigger.refresh();
        });
    </script>
</x-layout>