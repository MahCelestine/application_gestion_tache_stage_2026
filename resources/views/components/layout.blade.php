<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <title>Ca Com'mence Aujourd'hui - Gestionnaire de tâche</title>
</head>

<body class="w-[96%] mx-auto py-6 font-montserrat text-gray-900">
    @php
        $hasSideContent = (isset($searchBar) && $searchBar->isNotEmpty()) || (isset($ajoutTache) && $ajoutTache->isNotEmpty());
    @endphp
    <div class="w-full flex justify-between items-center mb-4">
        @if($hasSideContent)
            <div class="mr-4 basis-2/5 empty:hidden flex-1">{{ $searchBar ?? '' }}</div>
        @endif
        <nav @class([
            'mx-2 basis-2/5 flex justify-between text-lg flex-1' => $hasSideContent,
            'flex w-full justify-center gap-12 text-lg mt-3' => !$hasSideContent,
        ])>
            <a href="/" wire:navigate
                class="relative py-2 font-semibold transition-all duration-300 group {{ request()->is('/') ? 'text-blue-600' : 'text-gray-700' }}">
                Accueil
                <span
                    class="absolute bottom-0 left-0 h-[2px] transition-all duration-300 {{ request()->is('/') ? 'w-full bg-blue-600' : 'w-0 group-hover:w-full bg-gray-700' }}"></span>
            </a>
            <a href="/projet_cca" wire:navigate
                class="relative py-2 font-semibold transition-all duration-300 group {{ request()->is('projet_cca*') ? 'text-blue-600' : 'text-gray-700' }}">
                Projet CCA
                <span
                    class="absolute bottom-0 left-0 h-[2px] transition-all duration-300 {{ request()->is('projet_cca*') ? 'w-full bg-blue-600' : 'w-0 group-hover:w-full bg-gray-700' }}"></span>
            </a>
            <a href="/gestions" wire:navigate
                class="relative py-2 font-semibold transition-all duration-300 group {{ request()->is('gestions*') ? 'text-blue-600' : 'text-gray-700' }}">
                Gestion
                <span
                    class="absolute bottom-0 left-0 h-[2px] transition-all duration-300 {{ request()->is('gestions*') ? 'w-full bg-blue-600' : 'w-0 group-hover:w-full bg-gray-700' }}"></span>
            </a>
            <a href="/prospects" wire:navigate
                class="relative py-2 font-semibold transition-all duration-300 group {{ request()->is('prospects*') ? 'text-blue-600' : 'text-gray-700' }}">
                Prospect
                <span
                    class="absolute bottom-0 left-0 h-[2px] transition-all duration-300 {{ request()->is('prospects*') ? 'w-full bg-blue-600' : 'w-0 group-hover:w-full bg-gray-700' }}"></span>
            </a>
            <a href="/archives" wire:navigate
                class="relative py-2 font-semibold transition-all duration-300 group {{ request()->is('archives*') ? 'text-blue-600' : 'text-gray-700' }}">
                Archives
                <span
                    class="absolute bottom-0 left-0 h-[2px] transition-all duration-300 {{ request()->is('archives*') ? 'w-full bg-blue-600' : 'w-0 group-hover:w-full bg-gray-700' }}"></span>
            </a>
            <a href="{{ route('filament.admin.pages.dashboard') }}"
                class="relative py-2 font-semibold text-gray-700 transition-all duration-300 group">
                Admin
                <span
                    class="absolute bottom-0 left-0 h-[2px] bg-gray-700 transition-all duration-300 w-0 group-hover:w-full"></span>
            </a>
        </nav>
        @if ($hasSideContent)
            <div class="ml-4 basis-1/5 justify-end flex empty:hidden flex-1">{{ $ajoutTache ?? '' }}</div>
        @endif
    </div>
    <main>

        {{  $slot }}
    </main>

    @livewireScripts

    <script>
    function removeGhostOverlays() {
        document.body.style.overflow = 'auto';
        document.body.style.pointerEvents = 'auto';
        document.body.classList.remove('modal-open', 'overflow-hidden');
        
        const overlays = document.querySelectorAll('.modal-backdrop, [class*="backdrop-"], .fixed.inset-0.bg-black\\/50');
        overlays.forEach(overlay => {
            overlay.remove();
        });
    }

    document.addEventListener("livewire:navigate", (event) => {
        const fromUrl = window.location.pathname;
        
        document.addEventListener("livewire:navigated", function handleNav() {
            const toUrl = window.location.pathname;

            if (fromUrl === '/login' && toUrl === '/') {
                removeGhostOverlays();
                if (typeof ScrollTrigger !== 'undefined') {
                    ScrollTrigger.refresh();
                }
            }
            document.removeEventListener("livewire:navigated", handleNav);
        }, { once: true });
    });

    document.addEventListener("DOMContentLoaded", () => {
        try {
            const referrer = new URL(document.referrer);
            if (referrer.pathname === '/login' && window.location.pathname === '/') {
                removeGhostOverlays();
            }
        } catch (e) {
        }
    });
</script>
</body>

</html>