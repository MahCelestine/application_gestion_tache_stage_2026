<x-layout>
    <x-slot:searchBar>
        <div>
            <form action="{{ route('tasks.index') }}" method="GET" class="flex items-center">
                @csrf
                @if($filterStatus) <input type="hidden" name="filter_status" value="{{ $filterStatus }}" />@endif
                @if($sortClient) <input type="hidden" name="sort_client" value="{{ $sortClient }}" />@endif
                @if($sortTask) <input type="hidden" name="sort_task" value="{{ $sortTask }}" />@endif
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Client, tâche, sous-tâche ou une assignation..."
                    class="border-2 border-gray-300 py-2 px-6 w-[90%] rounded-4xl font-mono text-lg text-gray-600 focus:border-gray-400 focus:outline-gray-400 shadow-md" />
                <button type="submit" class="ml-3"><i class="bx bx-search text-3xl text-gray-700"></i></button>
            </form>
        </div>
    </x-slot:searchBar>
    <x-slot:ajoutTache>
        <a href="{{ route('tasks.create') }}"
            class="text-lg bg-blue-500 py-3 px-6 rounded-4xl text-white hover:bg-blue-600 shadow-md/20"> + Ajouter une
            grande tache</a>
    </x-slot:ajoutTache>
    <div class="flex w-[45%] items-center mb-2">
        <p class="font-semibold text-lg">Filtrer par état :</p>
        <div class="flex justify-around w-[55%]">
            @if ($filterStatus)
                <a href="{{ request()->fullUrlWithQuery(['filter_status' => null]) }}"
                    class="hover:font-semibold">Réinitialiser les filtres</a>
            @endif
            <a href="{{ request()->fullUrlWithQuery(['filter_status' => 'bloqué']) }}"
                class="hover:font-semibold {{ request('filter_status') == 'bloqué' ? 'font-semibold' : 'font-normal' }}">Bloqués</a>
            <a href="{{ request()->fullUrlWithQuery(['filter_status' => 'en cours']) }}"
                class="hover:font-semibold {{ request('filter_status') == 'en cours' ? 'font-semibold' : 'font-normal'  }}">En
                cours</a>
            <a href="{{ request()->fullUrlWithQuery(['filter_status' => 'validé']) }}"
                class="hover:font-semibold {{ request('filter_status') == 'validé' ? 'font-semibold' : 'font-normal' }}">Validés</a>
        </div>
    </div>
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg">
            <tr>
                <th class="py-6 rounded-tl-3xl border-t-2 border-b-2 border-l-2 border-gray-200">
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
                <th class="py-6 border-t-2 border-b-2 border-gray-200">
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
                <th class="py-6 border-t-2 border-b-2 border-gray-200">
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
                    <a href="{{ request()->fullUrlWithQuery(['sort_subtask' => $nextSubtaskSort]) }}">Sous tâche</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort_subtask' => $nextSubtaskSort]) }}">
                        {{ $arrowSubtask }}</a>
                </th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Etat</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Assignation</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Delai</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Temps donné</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Temps réel</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Compteur temps</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Devis</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-200">Facturation</th>
                <th class="py-6 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-200"></th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
            @if ($task->status !== 'validé' || $filterStatus || $search)
                <tbody class="task-group-border shadow-md">
                    <tr class="h-2">
                        <td colspan="12"></td>
                    </tr>
                    <tr class="text-lg">
                        <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                        <td class="border-r-[15px] border-r-transparent">{{ $task->label }}</td>
                        <td class="border-r-[15px] border-r-transparent"></td>
                        <td class="text-center border-r-[15px] border-r-transparent">
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
                        <td style="{{ $isUrgent ? 'color: #dc3545' : '' }}"
                            class="text-center border-r-[15px] border-r-transparent">
                            {{ $dueDate->format('d/m/Y') }}
                        </td>
                        <td class="text-center border-r-[15px] border-r-transparent">
                            {{ $task->formatDuration($task->estimated_hours) }}
                        </td>
                        <td class="text-center border-r-[15px] border-r-transparent">
                            {{ $task->formatDuration($task->actual_hours) }}
                        </td>
                        <td class="text-center border-r-[15px] border-r-transparent">{{ $task->compteur_temps }}</td>
                        <td class="border-r-[15px] border-r-transparent">{{ $task->quote_number ?? '-'}}</td>
                        <td class="border-r-[15px] border-r-transparent">{{ $task->billing_info ?? '-'}}</td>
                        <td><a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-500">Modifier</a></td>
                    </tr>
                    @if($task->status === 'bloqué' && $blocking = $task->currentBlocking())
                        <tr id="reason-task-{{ $task->id }}" style="display: none;">
                            <td colspan="3"></td>
                            <td colspan="5">Raison du blocage : {{ $blocking->description }}</td>
                            <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    @endif
                    <tr>
                        <td></td>
                        <td colspan="10" class="py-1">
                            <div class="border-b-2 border-gray-200 w-full"></div>
                        </td>
                        <td></td>
                    </tr>
                    @foreach ($task->subtasks as $subtask)
                        <tr>
                            <td></td>
                            <td><a href="{{ route('subtasks.edit', $subtask->id) }}" class="text-blue-500">Modifier</a></td>
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
                                <td colspan="3"></td>
                                <td colspan="5">Raison du blocage : {{ $blocking->description }}</td>
                                <td>Signalé le {{ $blocking->created_at->format('d/m à H:i') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @endif
                        </div>
                    @endforeach

                    <tr>
                        <td colspan="2"></td>
                        <td colspan="9"><a href="{{ route('subtasks.create', ['task_id' => $task->id]) }}" class="text-blue-500">+ Ajouter une sous
                                tâche</a></td>
                    </tr>
                </tbody>
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