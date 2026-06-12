<x-layout>
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-2xl shadow-sm border border-gray-100 mt-6 animate-fade-in">
        <div class="flex justify-between items-center pb-4 mb-1">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Résumé du jour pour : {{ $dailyAssignment->name }}</h1>
                <p class="text-sm text-gray-500">Historique des assignations et modifications importantes enregistrées
                    aujourd'hui.</p>
            </div>
        </div>
        <span class="block border-b-2 border-gray-300 w-[95%] m-auto mb-6"></span>
        <div>
            <h2 class="text-md font-bold mb-4 text-gray-800 flex items-center gap-2">
                Tâches & sous-tâches modifiées aujourd'hui
            </h2>

            <div class="overflow-x-auto">
                <table class="w-[100%] border-separate border-spacing-y-3">
                    <thead class="text-sm bg-gray-50 text-gray-700">
                        <tr>
                            <th
                                class="py-3 rounded-tl-3xl border-t-2 z-[51] border-b-2 border-l-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                                Client</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[11%] sticky top-0 bg-white">
                                Grande tâche</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Sous-tâche</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                                État</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Assignation</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Délai</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Quota</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Temps réel</th>
                            <th
                                class="py-3 rounded-tr-3xl border-t-2 z-[51] border-b-2 border-r-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Compteur</th>
                        </tr>
                    </thead>

                    @forelse ($updatedTasks as $task)
                        <tbody>
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="border-r-[15px] border-r-transparent py-2">{{ $task->client->nom ?? '-' }}</td>
                                <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600 py-2">
                                    {{ $task->label }}
                                </td>
                                <td class="text-gray-300 italic text-xs py-2"></td>
                                <td class="text-center py-2">
                                    <span class="{{ $task->status_cell_class }}">{{ $task->status }}</span>
                                </td>
                                <td class="border-r-[15px] border-r-transparent py-2">
                                    @forelse ($task->equipes as $membre)
                                        <span class="inline-block mr-1 font-medium"
                                            style="color: {{ $membre->color ?? '#272727' }}">{{ $membre->prenom }}</span>
                                    @empty <span class="text-gray-400">-</span> @endforelse
                                </td>
                                <td style="{{ $task->is_urgent ? 'color: #dc3545; font-weight: bold; ' : '' }}"
                                    class="text-center py-2">
                                    {{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center py-2">{{ $task->display_estimated ?? '-' }}</td>
                                <td class="text-center py-2">{{ $task->display_actual ?? '-' }}</td>
                                <td class="text-center py-2">{{ $task->compteur_temps ?? '-' }}</td>
                            </tr>

                            @foreach ($task->subtasks as $subtask)
                                <tr class="hover:bg-gray-50/40 transition">
                                    <td class="border-r-[15px] border-r-transparent py-2">{{ $task->client->nom ?? '-' }}</td>
                                    <td class="text-gray-400 text-xs italic pl-2 py-2"></td>
                                    <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600 py-2">
                                        {{ $subtask->label }}
                                    </td>
                                    <td class="text-center py-2">
                                        <span class="{{ $task->status_cell_class }}">{{ $subtask->status }}</span>
                                    </td>
                                    <td class="border-r-[15px] border-r-transparent py-2">
                                        @forelse ($subtask->equipes as $membre)
                                            <span class="inline-block mr-1 font-medium"
                                                style="color: {{ $membre->color ?? '#272727' }}">{{ $membre->prenom }}</span>
                                        @empty <span class="text-gray-400">-</span> @endforelse
                                    </td>
                                    <td style="{{ $task->is_urgent ? 'color: #dc3545; font-weight: bold; ' : '' }}"
                                        class="text-center py-2">
                                        {{ $subtask->due_date ? $subtask->due_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center py-2">{{ $subtask->display_estimated ?? '-' }}</td>
                                    <td class="text-center py-2">{{ $subtask->display_actual ?? '-' }}</td>
                                    <td class="text-center py-2">{{ $subtask->compteur_temps ?? '-' }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="9" class="py-2">
                                    <span class="block border-b-2 border-gray-200 w-[98%] m-auto"></span>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="9"
                                    class="text-center py-10 text-gray-400 bg-gray-50 border border-dashed rounded-xl italic">
                                    Aucune modification importante enregistrée aujourd'hui.
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>
        </div>
        <div class="mb-10">
            <h2 class="text-md font-bold mb-4 text-gray-800 flex items-center gap-2">
                Nouvelles tâches & sous-tâches créées / assignées aujourd'hui
            </h2>

            <div class="overflow-x-auto">
                <table class="w-[100%] border-separate border-spacing-y-3">
                    <thead class="text-sm bg-gray-50 text-gray-700">
                        <tr>
                            <th
                                class="py-3 rounded-tl-3xl border-t-2 z-[51] border-b-2 border-l-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                                Client</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[11%] sticky top-0 bg-white">
                                Grande tâche</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Sous-tâche</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                                État</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Assignation</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Délai</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Quota</th>
                            <th class="py-3 border-t-2 z-[51] border-b-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Temps réel</th>
                            <th
                                class="py-3 rounded-tr-3xl border-t-2 z-[51] border-b-2 border-r-2 border-gray-300 w-[7%] sticky top-0 bg-white">
                                Compteur</th>
                        </tr>
                    </thead>

                    @forelse ($createdTasks as $task)
                        <tbody>
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="border-r-[15px] border-r-transparent py-2">{{ $task->client->nom ?? '-' }}</td>
                                <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600 py-2">
                                    {{ $task->label }}</td>
                                <td class="text-gray-300 italic text-xs py-2"></td>
                                <td class="text-center py-2">
                                    <span class="{{ $task->status_cell_class }}">{{ $task->status }}</span>
                                </td>
                                <td class="border-r-[15px] border-r-transparent py-2">
                                    @forelse ($task->equipes as $membre)
                                        <span class="inline-block mr-1 font-medium"
                                            style="color: {{ $membre->color ?? '#272727' }}">{{ $membre->prenom }}</span>
                                    @empty <span class="text-gray-400">-</span> @endforelse
                                </td>
                                <td style="{{ $task->is_urgent ? 'color: #dc3545; font-weight: bold; ' : '' }}"
                                    class="text-center py-2">
                                    {{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center py-2">{{ $task->display_estimated ?? '-' }}</td>
                                <td class="text-center py-2">{{ $task->display_actual ?? '-' }}</td>
                                <td class="text-center py-2">{{ $task->compteur_temps ?? '-' }}</td>
                            </tr>
                            @foreach ($task->subtasks as $subtask)
                                <tr class="hover:bg-gray-50/40 transition">
                                    <td class="border-r-[15px] border-r-transparent py-2">{{ $task->client->nom ?? '-' }}</td>
                                    <td class="text-gray-400 text-xs italic pl-2 py-2"></td>
                                    <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600 py-2">
                                        {{ $subtask->label }}</td>
                                    <td class="text-center py-2">
                                        <span class="{{ $task->status_cell_class }}">{{ $subtask->status }}</span>
                                    </td>
                                    <td class="border-r-[15px] border-r-transparent py-2">
                                        @forelse ($subtask->equipes as $membre)
                                            <span class="inline-block mr-1 font-medium"
                                                style="color: {{ $membre->color ?? '#272727' }}">{{ $membre->prenom }}</span>
                                        @empty <span class="text-gray-400">-</span> @endforelse
                                    </td>
                                    <td style="{{ $task->is_urgent ? 'color: #dc3545; font-weight: bold; ' : '' }}"
                                        class="text-center py-2">
                                        {{ $subtask->due_date ? $subtask->due_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center py-2">{{ $subtask->display_estimated ?? '-' }}</td>
                                    <td class="text-center py-2">{{ $subtask->display_actual ?? '-' }}</td>
                                    <td class="text-center py-2">{{ $subtask->compteur_temps ?? '-' }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="9" class="py-1">
                                    <span class="block border-b-2 border-gray-200 w-[98%] m-auto"></span>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="9"
                                    class="text-center py-10 text-gray-400 bg-gray-50 border border-dashed rounded-xl italic">
                                    Aucune nouvelle tâche enregistrée aujourd'hui.
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>
        </div>

    </div>
</x-layout>