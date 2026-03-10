<x-layout>
    @foreach ( $tasks as $task)

    <p>{{ $task->client->nom }}</p>
    <p>{{ $task->label }}</p>
    <p>{{ $task->status }}</p>
    <p>{{ $task->due_date }}</p>
    <p>{{ $task->estimated_hours }}</p>
    
    @endforeach
</x-layout>