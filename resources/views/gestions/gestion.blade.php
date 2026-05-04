<x-layout>
    <x-slot:searchBar>
         <livewire:search-bar placeholder="Client, tâche..." />
    </x-slot:searchBar>
    <livewire:task-filters name="filter_payment" label="Filtrer par paiement" :options="[
        'a_facturer' => 'À facturer', 
        'non_paye' => 'Non payés', 
        'paye' => 'Payés'
    ]" :current="request('filter_payment', '')" />

    <livewire:gestion-list />

</x-layout>