<x-layout>
    <x-slot:searchBar>
        <x-search-bar :route="route('archives.archive')" />
    </x-slot:searchBar>
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
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Assignation</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Délai</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Temps donné</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Temps réel</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Compteur temps</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Devis</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[8%]">Facturation</th>
                <th class="py-6 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[8%]">Payé</th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
            @if($task->is_paid)
                <tbody class="task-group-border shadow-md">
                    <tr class="h-2">
                        <td colspan="11"></td>
                    </tr>
                    <tr class="text-lg">
                        <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                        <td class="border-r-[15px] border-r-transparent">{{ $task->label }}</td>
                        <td class="border-r-[15px] border-r-transparent"></td>
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
                        <td style="" class="text-center ">
                            {{ $dueDate->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            {{ $task->formatDuration($task->estimated_hours) }}
                        </td>
                        <td class="text-center">
                            {{ $task->formatDuration($task->actual_hours) }}
                        </td>
                        <td class="text-center">{{ $task->compteur_temps }}</td>
                        <td class="text-center">{{ $task->quote_number ?? '-'}}</td>
                        <td class="text-center">{{ $task->billing_info ?? '-'}}</td>
                        <td class="text-center">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-400">
                                {{ $task->is_paid ? 'Payé' : 'Non payé' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="9" class="py-1">
                            <div class="border-b-2 border-gray-300 w-full"></div>
                        </td>
                        <td></td>
                    </tr>
                    @foreach ($task->subtasks as $subtask)
                        @if ($subtask->is_paid)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $subtask->label }}</td>
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
                                <td style="" class="text-center">
                                    {{ $dueDate->format('d/m/Y') }}
                                </td>
                                <td class="text-center">{{ $subtask->formatDuration($subtask->estimated_hours) }}</td>
                                <td class="text-center">{{ $subtask->formatDuration($subtask->actual_hours) }}</td>
                                <td class="text-center">{{ $subtask->compteur_temps }}</td>
                                <td class="text-center">{{ $subtask->quote_number ?? '-' }}</td>
                                <td class="text-center">{{ $subtask->billing_info ?? '-' }}</td>
                                <td class="text-center">
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-400">
                                        {{ $task->is_paid ? 'Payé' : 'Non payé' }}
                                    </span>
                                </td>
                            </tr>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            @endif
        @endforeach
    </table>

</x-layout>