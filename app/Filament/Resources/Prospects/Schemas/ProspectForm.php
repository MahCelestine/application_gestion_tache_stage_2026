<?php

namespace App\Filament\Resources\Prospects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProspectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required(),
                Select::make('status')
                    ->options(['RDV à prendre' => 'R d và prendre', 'Date de RDV' => 'Date de r d v', 'OK' => 'O k'])
                    ->default('RDV à prendre')
                    ->required(),
                DatePicker::make('rdv_date'),
                Select::make('response_type')
                    ->options(['OUI' => 'O u i', 'NON' => 'N o n', 'DEVIS' => 'D e v i s']),
                TextInput::make('quote_number'),
                Select::make('is_followup')
                    ->options(['OUI' => 'O u i', 'NON' => 'N o n']),
                TextInput::make('source'),
            ]);
    }
}
