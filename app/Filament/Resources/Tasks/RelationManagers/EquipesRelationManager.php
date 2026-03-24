<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use App\Filament\Resources\Equipes\EquipeResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipesRelationManager extends RelationManager
{
    protected static string $relationship = 'equipes';

    protected static ?string $relatedResource = EquipeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prenom')
                    ->label('Prénom'),
                TextColumn::make('nom')
                    ->label('Nom'),
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}