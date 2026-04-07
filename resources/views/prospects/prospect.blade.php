<x-layout>
    <x-slot:searchBar>
        <x-search-bar :route="route('prospects.prospect')" placeholder="Nom du prospect..." />
    </x-slot:searchBar>
    <x-slot:ajoutTache>
        <a href="{{ route('prospects.create') }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 shadow-md/20"> + Ajouter un
            prospect</a>
    </x-slot:ajoutTache>
    <x-filter-bar name="filter_status" label="Filtrer par état" :options="[
        'RDV à prendre' => 'RDV à prendre',
        'Date de RDV' => 'Date de RDV',
        'OK' => 'OK'
    ]" />
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg">
            <tr>
                <th class="py-6 rounded-tl-3xl border-t-2 border-b-2 border-l-2 border-gray-300 w-[12%]">
                    @php
                        $nextNomSort = match ($sortNom) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };
                        $arrowNom = match ($sortNom) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_nom' => $nextNomSort]) }}">Nom</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_nom' => $nextNomSort]) }}">
                        {{ $arrowNom }}</a>
                </th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[9%]">État</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[10%]">Date du RDV</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Réponses</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Relance</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Source</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[20%]">Info complémentaire</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]"></th>
                <th class="py-6 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[18%]"></th>
            </tr>
        </thead>
        @foreach ($prospects as $prospect)
            <tbody class="task-group-border shadow-md">
                <tr>
                    <td colspan="9"></td>
                </tr>
                <tr class="text-lg">
                    <td class="border-r-[15px] border-r-transparent">{{ $prospect->nom }}</td>
                    @if($prospect->status !== "RDV à prendre")
                        <td class="text-center">{{ $prospect->status }}</td>
                        <td class="text-center">
                            {{ $prospect->rdv_date ? $prospect->rdv_date->format('d/m/Y') : '-' }}
                        </td>
                    @else
                        <td class="text-center">{{ $prospect->status }}</td>
                        <td class="text-center">-</td>
                    @endif
                    <td class="text-center">
                        @if ($prospect->status === 'OK')
                            @if(strtoupper($prospect->response_type) === 'DEVIS' && $prospect->quote_number)
                                n° {{ $prospect->quote_number }}
                            @else
                                {{ $prospect->response_type }}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="txet-center">
                        @if($prospect->status === 'OK')
                            {{ $prospect->is_followup }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $prospect->source }}</td>
                    @if($prospect->notes->isNotEmpty())
                        <td class="border-r-[15px] border-r-transparent">{{ $prospect->notes->last()->description }}</td>
                    @else
                        <td class="border-r-[15px] border-r-transparent">Aucune note</td>
                    @endif
                    <td class="border-r-[15px] border-r-transparent  text-center"><a
                            href="{{ route('prospects.edit', $prospect->id) }}"
                            class="text-blue-500 hover:text-blue-600 hover:font-semibold">Modifier</a></td>
                    <td class="border-r-[15px] border-r-transparent  text-center"><a
                            href="{{ route('prospects.transform', $prospect->id) }}"
                            class="text-blue-500 hover:text-blue-600 hover:font-semibold">Transformer
                            en grande tâche</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>
</x-layout>