@props([
    'route',
    'search' => request('search'),
    'filterStatus' => request('filter_status'),
    'sortNom' => request('sort_nom'),
    'placeholder' => 'Nom ...'
])

<div {{ $attributes->merge(['class' => '']) }}>
    <form action="{{ $route }}" method="GET" class="flex items-center">
        @csrf
        @if($filterStatus) <input type="hidden" name="filter_status" value="{{ $filterStatus }}" /> @endif
        @if($sortNom) <input type="hidden" name="sort_nom" value="{{ $sortNom }}" /> @endif
        <input 
            type="text" 
            name="search" 
            value="{{ $search }}"
            placeholder="{{ $placeholder }}"
            class="border-2 border-gray-300 py-2 px-6 w-[90%] rounded-4xl font-mono text-lg text-gray-600 focus:border-gray-400 focus:outline-gray-400 shadow-md" 
        />
        <button type="submit" class="ml-3 transition-transform hover:scale-110">
            <i class="bx bx-search text-3xl text-gray-700"></i>
        </button>
    </form>
</div>