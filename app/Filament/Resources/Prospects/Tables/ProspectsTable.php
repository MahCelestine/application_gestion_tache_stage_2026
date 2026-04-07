<?php

namespace App\Filament\Resources\Prospects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProspectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'OK' => 'success',           // Vert
                        'Date de RDV' => 'warning',  // Orange
                        'RDV à prendre' => 'info',   // Bleu
                        default => 'gray',           // Gris
                    })
                    ->sortable(),
                TextColumn::make('rdv_date')
                    ->label('Date RDV')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('response_type')
                    ->label('Réponses')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'OUI' => 'success',   // Vert
                        'DEVIS' => 'warning', // Orange
                        'NON' => 'danger',    // Rouge
                        default => 'gray',
                    }),
                TextColumn::make('quote_number')
                    ->label('N° Devis')
                    ->searchable(),
                TextColumn::make('is_followup')
                    ->label('Relance')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'OUI' => 'success', // Vert
                        'NON' => 'danger',  // Rouge
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
