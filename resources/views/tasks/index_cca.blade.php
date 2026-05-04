<x-layout>
    <x-slot:ajoutTache>
        <a href="{{ route('tasks.create', ['context' => 'cca']) }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 shadow-md/20"> + Ajouter une
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