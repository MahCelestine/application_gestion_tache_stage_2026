<x-layout>
    <x-slot:searchBar>
        <x-search-bar :route="route('gestions.gestion')" />
    </x-slot:searchBar>
    <x-filtrage-paiement />
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg">
            <tr>
                <th class="py-6 rounded-tl-3xl border-t-2 border-b-2 border-l-2 border-gray-300 w-[14%]">
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
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[15%]">
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
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[16%]">
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
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[14%]">Devis</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[14%]">Facturation</th>
                <th class="py-6 border-t-2 border-b-2 border-gray-300 w-[15%]">Payé</th>
                <th class="py-6 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[12%]"></th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
            <tbody class="task-group-border shadow-md">
                <tr class="h-2">
                    <td colspan="12"></td>
                </tr>
                <tr class="text-lg">
                    <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                    <td class="border-r-[15px] border-r-transparent">{{ $task->label }}</td>
                    <td></td>
                    <td class="border-r-[15px] border-r-transparent">{{ $task->quote_number ?? '-'}}</td>
                    @if ($task->status === 'validé')
                        <td class="border-r-[15px] border-r-transparent">{{ $task->billing_info ?? '-'}}</td>
                        <td class="text-center">
                            @if($task->is_paid == 'oui')
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-400">
                                    Payé
                                </span>
                            @else
                                @if ($task->billing_info === null)
                                    <div class="bg-orange-400 w-[20px] h-[20px] rounded-3xl m-auto"></div>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-400">
                                        Non payé
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td><a href="{{ route('gestions.task_edit', $task->id) }}"
                                class="text-blue-500 hover:text-blue-600 hover:font-semibold">Modifier</a></td>
                    @else
                        <td colspan="3">Impossible de facturer si la tâche n'est pas finie</td>
                    @endif
                </tr>
                <tr>
                    <td></td>
                    <td colspan="5" class="py-1">
                        <div class="border-b-2 border-gray-300 w-full"></div>
                    </td>
                    <td></td>
                </tr>
                @foreach ($task->subtasks as $subtask)
                    <tr>
                        <td></td>
                        <td><a href="{{ route('gestions.subtask_edit', $subtask->id) }}"
                                class="text-blue-500 hover:text-blue-600 hover:font-semibold">Modifier</a></td>
                        <td>{{ $subtask->label }}</td>
                        <td>{{ $subtask->quote_number ?? '-' }}</td>
                        <td>{{ $subtask->billing_info ?? '-' }}</td>
                        <td class="text-center">
                            @if($subtask->is_paid == 'oui')
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-400">
                                    Payé
                                </span>
                            @else
                                @if ($subtask->billing_info === null)
                                    <div class="bg-orange-400 w-[20px] h-[20px] rounded-3xl m-auto"></div>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-400">
                                        Non payé
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    </div>
                @endforeach
            </tbody>
        @endforeach
    </table>

</x-layout>