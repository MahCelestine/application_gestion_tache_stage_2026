<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SubtasksRelationManager extends RelationManager
{
    protected static string $relationship = 'subtasks';

    protected static ?string $relatedResource = TaskResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable(),

                TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y'),

                TextColumn::make('estimated_hours')
                    ->label('Est.')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' h'),

                TextColumn::make('actual_hours')
                    ->label('Réel')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' h'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'en cours' => 'warning',
                        'validé' => 'success',
                        'bloqué' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([

            ]);
    }
}
