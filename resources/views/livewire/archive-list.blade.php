<div>
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg z-10 bg-white">
            <tr>
                <th
                    class="py-3 rounded-tl-3xl border-t-2 z-11 border-b-2 border-l-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Client')">Client
                        {{$sortClient === 'asc' ? '(A-Z)' : ($sortClient === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[11%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Task')">Grande tâches
                        {{$sortTask === 'asc' ? '(A-Z)' : ($sortTask === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[17%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Subtask')">Sous-tâches
                        {{$sortSubtask === 'asc' ? '(A-Z)' : ($sortSubtask === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Assignation</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Délai</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Temps donné</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Temps réel</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Compteur temps</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Devis</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[8%] sticky top-0 bg-white">Facturation</th>
                <th
                    class="py-3 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[8%] sticky top-0 bg-white">
                    Payé</th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
                    <tbody wire:key="task-group-{{ $task->id }}" x-data x-init="
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
                    "
                    class="task-group-border shadow-md">
                        <tr class="h-2">
                            <td colspan="11"></td>
                        </tr>
                        <tr>
                            <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                            <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600">{{ $task->label }}</td>
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
                                {{ $task->formatTime($task->estimated_hours) }}
                            </td>
                            <td class="text-center">
                                {{ $task->formatTime($task->actual_hours) }}
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
                                            <tr class="{{ $loop->last ? 'last-subtask-row' : '' }}">
                                                <td colspan="2" class="{{ $loop->last ? 'pb-[10px]' : '' }}"></td>
                                                <td class="{{ $loop->last ? 'pb-[10px]' : '' }}">{{ $subtask->label }}</td>
                                                <td class="{{ $loop->last ? 'pb-[10px]' : '' }}">
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
                                                <td style="" class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">
                                                    {{ $dueDate->format('d/m/Y') }}
                                                </td>
                                                <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">
                                                    {{ $subtask->formatTime($subtask->estimated_hours) }}
                                                </td>
                                                <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">
                                                    {{ $subtask->formatTime($subtask->actual_hours) }}
                                                </td>
                                                <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">{{ $subtask->compteur_temps }}</td>
                                                <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">{{ $subtask->quote_number ?? '-' }}</td>
                                                <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">{{ $subtask->billing_info ?? '-' }}</td>
                                                <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">
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
        @endforeach
</table>
<div class="mt-8 pagination-container">
    {{ $tasks->links() }}
</div>
</div>