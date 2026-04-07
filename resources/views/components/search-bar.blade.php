@props([
    'route',
    'placeholder' => 'Rechercher...'
])

@php
    $queryParams = request()->except('search');
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    <form action="{{ $route }}" method="GET" class="flex items-center">
        @csrf
        @foreach ( $queryParams as $key => $value )
            @if($value !== null) 
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" /> 
            @endif
        @endforeach
        <div class="w-[90%]">
        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}"
            placeholder="{{ $placeholder }}"
            class="border-2 border-gray-300 py-2 px-6 rounded-4xl w-full font-mono text-lg text-gray-600 focus:border-gray-400 focus:outline-gray-400 shadow-md" 
        />
        @if(request('search'))
            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" 
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                <i class="bx bx-x text-2xl"></i>
            </a>
        @endif
        </div>
        <button type="submit" class="ml-3 transition-transform hover:scale-110">
            <i class="bx bx-search text-3xl text-gray-700"></i>
        </button>
    </form>
</div>