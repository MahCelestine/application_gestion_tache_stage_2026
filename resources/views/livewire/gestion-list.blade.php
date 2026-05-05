<div>
    <table class="w-[100%] border-separate border-spacing-y-4">
        <thead class="text-lg z-10 bg-white">
            <tr>
                <th class="py-3 rounded-tl-3xl border-t-2 border-b-2 z-11 border-l-2 border-gray-300 w-[14%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Client')">Client
                        {{$sortClient === 'asc' ? '(A-Z)' : ($sortClient === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[15%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Task')">Grande tâche
                        {{$sortTask === 'asc' ? '(A-Z)' : ($sortTask === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[16%] sticky top-0 bg-white">
                    <button wire:click="sortBy('Subtask')">Sous-tâche
                        {{$sortSubtask === 'asc' ? '(A-Z)' : ($sortSubtask === 'desc' ? '(Z-A)' : '') }}</button>
                </th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[14%] sticky top-0 bg-white">Devis</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[14%] sticky top-0 bg-white">Facturation</th>
                <th class="py-3 border-t-2 border-b-2 border-gray-300 w-[15%] sticky top-0 bg-white">Payé</th>
                <th class="py-3 rounded-tr-3xl border-t-2 border-b-2 border-r-2 border-gray-300 w-[12%] sticky top-0 bg-white"></th>
            </tr>
        </thead>
        @foreach ($tasks as $task)
            <tbody wire:key="task-group-{{ $task->id }}" 
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
        " 
        class="task-group-border shadow-md">
                <tr class="h-2">
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="border-r-[15px] border-r-transparent">{{ $task->client->nom }}</td>
                    <td class="border-r-[15px] border-r-transparent font-semibold text-gray-600">{{ $task->label }}</td>
                    <td></td>
                    <td class="text-center">{{ $task->quote_number ?? '-'}}</td>
                    @if ($task->status === 'validé')
                        <td class="text-center">{{ $task->billing_info ?? '-'}}</td>
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
                                                            class="btn-fill-animation inline-block relative z-10 text-blue-500 border border-blue-500 rounded-2xl py-1 px-3 overflow-hidden transition-colors duration-300">
                                                            <span class="relative z-20">Modifier</span></td>
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
                    <tr class="{{ $loop->last ? 'last-subtask-row' : '' }}">
                        <td class="{{ $loop->last ? 'pb-[10px]' : '' }}"></td>
                        <td><a href="{{ route('gestions.subtask_edit', $subtask->id) }}"
                                                            class="btn-fill-animation inline-block relative z-10 text-blue-500 border border-blue-200 rounded-2xl py-1 px-3 overflow-hidden transition-colors duration-300">
                                                            <span class="relative z-20">Modifier</span></td>
                        <td>{{ $subtask->label }}</td>
                        <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">{{ $subtask->quote_number ?? '-' }}</td>
                        <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">{{ $subtask->billing_info ?? '-' }}</td>
                        <td class="text-center {{ $loop->last ? 'pb-[10px]' : '' }}">
                            @if($subtask->is_paid == true)
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
</div>
