<?php

namespace App\Filament\Resources\Subtasks;

use App\Models\Subtask;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use BackedEnum;
use App\Filament\Resources\Tasks\RelationManagers\EquipesRelationManager;

// Import des pages
use App\Filament\Resources\Subtasks\Pages\CreateSubtask;
use App\Filament\Resources\Subtasks\Pages\EditSubtask;
use App\Filament\Resources\Subtasks\Pages\ListSubtasks;

class SubtaskResource extends Resource
{
    protected static ?string $model = Subtask::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informations Sous-Tâche')
                    ->schema([
                        TextInput::make('label')
                            ->required(),

                        // Ajout de la date d'échéance
                        DatePicker::make('due_date')
                            ->label('Date d\'échéance'),

                        // Ajout des heures (estimées et réelles)
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('estimated_hours')
                                    ->label('Heures estimées')
                                    ->numeric()
                                    ->suffix('h'),

                                TextInput::make('actual_hours')
                                    ->label('Heures réelles')
                                    ->numeric()
                                    ->suffix('h'),
                            ]),

                        Select::make('status')
                            ->options([
                                'en cours' => 'En cours',
                                'bloqué' => 'Bloqué',
                                'terminé' => 'Terminé',
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make('Référence Tâche Parente')
                    ->description('Cette section est en lecture seule.')
                    ->schema([
                        Select::make('task_id')
                            ->relationship('task', 'label')
                            ->label('Tâche associée')
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "ID: {$record->id} - {$record->label}")
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('label')->searchable(),

                // Affichage de la date d'échéance
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable(),

                // Affichage des heures
                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label('Est.')
                    ->suffix(' h'),

                Tables\Columns\TextColumn::make('actual_hours')
                    ->label('Réel')
                    ->suffix(' h'),

                Tables\Columns\TextColumn::make('task.label')
                    ->label('Tâche Parente')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'en cours' => 'warning',
                        'bloqué' => 'danger',
                        'terminé' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EquipesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubtasks::route('/'),
            'create' => CreateSubtask::route('/create'),
            'edit' => EditSubtask::route('/{record}/edit'),
        ];
    }
}