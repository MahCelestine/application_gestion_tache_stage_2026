<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Ca Com'mence Aujourd'hui - Gestionnaire de tâche</title>
</head>

<body class="w-[96%] mx-auto py-6 font-montserrat text-gray-900">
    @php
        $hasSideContent = (isset($searchBar) && $searchBar->isNotEmpty()) || (isset($ajoutTache) && $ajoutTache->isNotEmpty());
    @endphp
    <div class="w-full flex justify-between items-center mb-8">
        @if($hasSideContent)
            <div class="mr-4 basis-2/5 empty:hidden flex-1">{{ $searchBar ?? '' }}</div>
        @endif
        <nav @class([
            'mx-2 basis-2/5 flex justify-between text-lg flex-1' => $hasSideContent,
            'flex w-full justify-center gap-12 text-lg mt-3' => !$hasSideContent,
        ])>
            <a href="/" class="font-semibold hover:font-normal hover:tracking-wide">Accueil</a>
            <a href="/projet_cca" class="font-semibold hover:font-normal hover:tracking-wide">Projet CCA</a>
            <a href="/gestions" class="font-semibold hover:font-normal hover:tracking-wide">Gestion</a>
            <a href="/prospects" class="font-semibold hover:font-normal hover:tracking-wide">Prospect</a>
            <a href="/archives" class="font-semibold hover:font-normal hover:tracking-wide">Archives</a>
            <a href="{{ route('filament.admin.pages.dashboard') }}"
                class="btn font-semibold hover:font-normal hover:tracking-wide">
                Administration
            </a>
        </nav>
        @if ($hasSideContent)
            <div class="ml-4 basis-1/5 justify-end flex empty:hidden flex-1">{{ $ajoutTache ?? '' }}</div>
        @endif
    </div>

    <main>
        {{  $slot }}
    </main>
</body>

</html>