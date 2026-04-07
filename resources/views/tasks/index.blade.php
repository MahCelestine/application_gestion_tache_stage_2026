<x-layout>
    <x-slot:searchBar>
        <x-search-bar :route="route('tasks.index')" placeholder="Client, tâche ou assignation..."/>
    </x-slot:searchBar>
    <x-slot:ajoutTache>
        <a href="{{ route('tasks.create') }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 shadow-md/20"> + Ajouter une
            grande tâche</a>
    </x-slot:ajoutTache>
    <x-filter-bar name="filter_status" label="Filtrer par état" :options="[
        'en cours' => 'En cours',
        'bloqué' => 'Bloqués',
        'validé' => 'Validés'
    ]" />
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg">
            <tr>
                <th class="py-6 rounded-tl-3xl border-t-2 border-b-2 border-l-2 border-gray-300 w-[8%]">
                    @php
                        $nextClientSort = match ($sortClient) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };
                        $arrowClient = match ($sortClient) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_client' => $nextClientSort]) }}">Client</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_client' => $nextClientSort]) }}">
                        {{ $arrowClient }}</a>
                </th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[11%]">
                    @php
                        $nextTaskSort = match ($sortTask) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };

                        $arrowTask = match ($sortTask) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_task' => $nextTaskSort]) }}">Grande tâche</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_task' => $nextTaskSort]) }}"> {{ $arrowTask }}</a>
                </th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[17%]">
                    @php
                        $nextSubtaskSort = match ($sortSubtask) {
                            'asc' => 'desc',
                            'desc' => '',
                            default => 'asc',
                        };

                        $arrowSubtask = match ($sortSubtask) {
                            'asc' => '(A-Z)',
                            'desc' => '(Z-A)',
                            default => '',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_subtask' => $nextSubtaskSort]) }}">Sous-tâche</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_subtask' => $nextSubtaskSort]) }}">
                        {{ $arrowSubtask }}</a>
                </th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">État</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Assignation</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Délai</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Temps donné</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Temps réel</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Compteur temps</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Devis</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[7%]">Facturation</th>
                <th class="py-6 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[7%]"></th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
            @if (!($task->status === 'validé' && $filterStatus === 'validé'))
                @if ($task->status !== 'validé' || $filterStatus || $search)
                    <tbody class="task-group-border shadow-md">
                        <tr class="h-2">
                            <td colspan="12"></td>
                        </tr>
                        <tr class="text-lg">
                            <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                            <td class="border-r-[15px] border-r-transparent">{{ $task->label }}</td>
                            <td class="border-r-[15px] border-r-transparent"></td>
                            <td class="text-center">
                                <span class="cell-{{ Str::slug($task->status) }}">{{ $task->status }}
                                    @if($task->status === 'bloqué' && $task->currentBlocking() && $task->subtasks->count() === 0)
                                        <button type="button" onclick="toggleReason('reason-task-{{ $task->id }}')">↓</button>
                                    @endif
                                </span>
                            </td>
                            <td class="border-r-[15px] border-r-transparent">
                                @forelse ($task->equipes as $membre)
                                    {{ $membre->prenom }}
                                @empty
                                    -
                                @endforelse
                            </td>
                            @php
                                $dueDate = \Carbon\Carbon::parse($task->due_date);
                                $isUrgent = $dueDate->lte(now()->addDays(7));
                            @endphp
                            <td style="{{ $isUrgent ? 'color: #dc3545' : '' }}" class="text-center ">
                                {{ $dueDate->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                {{ $task->formatDuration($task->estimated_hours) }}
                            </td>
                            <td class="text-center">
                                {{ $task->formatDuration($task->actual_hours) }}
                            </td>
                            <td class="text-center">{{ $task->compteur_temps }}</td>
                            <td class="border-r-[15px] border-r-transparent">{{ $task->quote_number ?? '-'}}</td>
                            <td class="border-r-[15px] border-r-transparent">{{ $task->billing_info ?? '-'}}</td>
                            <td><a href="{{ route('tasks.edit', $task->id) }}"
                                    class="text-blue-500 hover:text-blue-600 hover:font-semibold">Modifier</a></td>
                        </tr>
                        @if($task->status === 'bloqué' && $blocking = $task->currentBlocking())
                            <tr id="reason-task-{{ $task->id }}" style="display: none;">
                                <td colspan="2"></td>
                                <td colspan="6">Raison du blocage : {{ $blocking->description }}</td>
                                <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @endif
                        <tr>
                            <td></td>
                            <td colspan="10" class="py-1">
                                <div class="border-b-2 border-gray-300 w-full"></div>
                            </td>
                            <td></td>
                        </tr>
                        @foreach ($task->subtasks as $subtask)
                            <tr>
                                <td></td>
                                <td><a href="{{ route('subtasks.edit', $subtask->id) }}"
                                        class="text-blue-500 hover:text-blue-600 hover:font-semibold">Modifier</a></td>
                                <td>{{ $subtask->label }}</td>
                                <td class="text-{{ Str::slug($subtask->status) }} text-center">{{ $subtask->status }}
                                    @if($subtask->status === 'bloqué')
                                        <button type="button" onclick="toggleReason('reason-sub-{{ $subtask->id }}')">↓</button>
                                    @endif
                                </td>
                                <td>
                                    @forelse ($subtask->equipes as $membre)
                                        {{ $membre->prenom }}
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                @php
                                    $dueDate = \Carbon\Carbon::parse($subtask->due_date);
                                    $isUrgent = $dueDate->lte(now()->addDays(7));
                                @endphp
                                <td style="{{ $isUrgent ? 'color: #dc3545' : '' }}" class="text-center">
                                    {{ $dueDate->format('d/m/Y') }}
                                </td>
                                <td class="text-center">{{ $subtask->formatDuration($subtask->estimated_hours) }}</td>
                                <td class="text-center">{{ $subtask->formatDuration($subtask->actual_hours) }}</td>
                                <td class="text-center">{{ $subtask->compteur_temps }}</td>
                                <td>{{ $subtask->quote_number ?? '-' }}</td>
                                <td>{{ $subtask->billing_info ?? '-' }}</td>
                            </tr>
                            @if($subtask->status === 'bloqué' && $blocking = $subtask->currentBlocking())
                                <tr id="reason-sub-{{ $subtask->id }}" style="display: none;">
                                    <td colspan="2"></td>
                                    <td colspan="6">Raison du blocage : {{ $blocking->description }}</td>
                                    <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif
                            </div>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="9"><a href="{{ route('subtasks.create', ['task_id' => $task->id]) }}"
                                    class="text-blue-500 hover:text-blue-600 hover:font-semibold">+ Ajouter une sous-tâche</a></td>
                        </tr>
                    </tbody>
                @endif
            @endif
        @endforeach
    </table>

    <script>
        function toggleReason(id) {
            const row = document.getElementById(id);
            if (row.style.display === "none") {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        }
    </script>

</x-layout>