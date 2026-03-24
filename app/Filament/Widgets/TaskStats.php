<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\Tasks\Tables\TasksTable;

class TaskStats extends BaseWidget
{
    // Le widget prend toute la largeur pour un meilleur confort de lecture
    protected int|string|array $columnSpan = 'full';

    // Titre du bloc sur le Dashboard
    protected static ?string $heading = 'Suivi des Tâches';

    // Position du widget (1 = en haut de page)
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        // On récupère la configuration que tu as déjà définie dans ton TasksTable
        return TasksTable::configure($table)
            ->query(
                // On affiche les tâches les plus récentes en priorité
                Task::query()->latest()
            )
            ->paginated([5, 10]); // On limite à 5 ou 10 résultats pour garder un dashboard propre
    }
}