<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('label')
                    ->required(),
                Select::make('status')
                    ->options(['en cours' => 'En cours', 'validé' => 'Validé', 'bloqué' => 'Bloqué'])
                    ->default('en cours')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                TextInput::make('quote_number'),
                TextInput::make('billing_info'),
                TextInput::make('estimated_hours')
                    ->required()
                    ->numeric(),
                TextInput::make('actual_hours')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Toggle::make('is_paid')
                    ->required(),
            ]);
    }
}
