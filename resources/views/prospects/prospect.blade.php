<x-layout>
    <x-slot:searchBar>
        <livewire:search-bar placeholder="Nom du prospect..." />
    </x-slot:searchBar>
    <x-slot:ajoutTache>
        <a href="{{ route('prospects.create') }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 transition-all transition-all duration-150 shadow-[0_4px_2px_0_rgba(0,0,0,0.1)] hover:translate-y-[2px] active:translate-y-[4px] hover:shadow-[0_2px_5px_0_rgba(0,0,0,0.2)]">
            + Ajouter un
            prospect</a>
    </x-slot:ajoutTache>
    <livewire:task-filters name="filter_status" label="Filtrer par état" :options="[
        'RDV à prendre' => 'RDV à prendre',
        'Date de RDV' => 'Date de RDV',
        'OK' => 'OK'
    ]" :current="request('filter_status', '')" />
    <livewire:prospect-list />
</x-layout>