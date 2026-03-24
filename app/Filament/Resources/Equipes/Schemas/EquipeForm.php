<?php

namespace App\Filament\Resources\Equipes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema; // Importez Schema au lieu de Form

class EquipeForm
{
    // Changez Form $form par Schema $schema
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([ // En v5, on utilise souvent components() sur le Schema
                TextInput::make('nom')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('prenom')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}