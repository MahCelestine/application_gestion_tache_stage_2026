<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'en cours' => 'info', // bleu
                        'attente BAT' => 'warning',  // orange
                        'BAT ok' => 'primary',  // bleu foncé
                        'validé' => 'success',   // Vert
                        'bloqué' => 'danger',    // Rouge
                        default => 'gray',       // Gris par défaut
                    }),
                TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estimated_hours')
                    ->label('Heures estimées')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->sortable(),
                TextColumn::make('actual_hours')
                    ->label('Heures réel')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->sortable(),
                TextColumn::make('quote_number')
                    ->searchable(),
                TextColumn::make('billing_info')
                    ->searchable(),
                IconColumn::make('is_paid')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
