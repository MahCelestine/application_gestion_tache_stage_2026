<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Ca Com'mence Aujourd'hui - Gestionnaire de tâche</title>
</head>
<body>
    <nav>
        <a href="/"> Accueil </a>
    </nav>

    <main>
        {{  $slot }}
    </main>
</body>
</html>