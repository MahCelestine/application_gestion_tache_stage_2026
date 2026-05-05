<div>
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg z-10 bg-white">
            <tr>
                <th
                    class="py-3 rounded-tl-3xl z-11 border-t-2 border-b-2 border-l-2 border-gray-300 w-[12%] sticky top-0 bg-white">
                    <button wire:click="sortByNom()">Nom
                        {{$sortNom === 'asc' ? '(A-Z)' : ($sortNom === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[9%] sticky top-0 bg-white">État</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[10%] sticky top-0 bg-white">Date du RDV</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Réponses</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">Relance</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Source</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[20%] sticky top-0 bg-white">Info complémentaire
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white"></th>
                <th
                    class="py-3 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[18%] sticky top-0 bg-white">
                </th>
            </tr>
        </thead>
        @foreach ($prospects as $prospect)
            <tbody wire:key="prospect-group-{{ $prospect->id }}" 
        x-data 
        x-init="
            gsap.fromTo($el, 
                { opacity: 0, y: 40, scale: 0.98 }, 
                { 
                    opacity: 1, 
                    y: 0, 
                    scale: 1,
                    duration: 0.8, 
                    ease: 'back.out(1.4)',
                    scrollTrigger: {
                        trigger: $el,
                        start: 'top 92%',
                        toggleActions: 'play none none none'
                    }
                }
            )
        " class="task-group-border shadow-md">
                <tr>
                    <td colspan="9"></td>
                </tr>
                <tr class="last:pb-[5px]">
                    <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600">{{ $prospect->nom }}</td>
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
                    <td class="text-center">
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
                            class="text-blue-500 hover:text-blue-600 hover:font-semibold border rounded-2xl py-1 px-2 border-blue-500">Modifier</a>
                    </td>
                    <td class="border-r-[15px] border-r-transparent  text-center">
                        <form action="{{ route('prospects.transform', $prospect->id) }}" method="GET"
                            id="transform-form-{{ $prospect->id }}" style="display: none;">
                            @csrf

                        </form>
                        <button type="button"
                            onclick="Livewire.dispatch('open-delete-modal', { title: 'Transformer le prospect', message: 'Êtes-vous sûr de vouloir transformer ce prospect en client ? Cette action est irréversible.', label: 'Transformer', formId: 'transform-form-{{ $prospect->id }}' })"
                            class="text-blue-500 hover:text-blue-600 hover:font-semibold">Transformer
                            en grande tâche</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>

    <livewire:loading-overlay />
    <livewire:delete-confirmation-modal />

    <script>

        window.addEventListener('do-submit-delete', event => {
            const form = document.getElementById(event.detail.formId);
            if (form) {
                const overlay = document.getElementById('loading-overlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }
                form.submit();
            }
        });

    </script>
</div>