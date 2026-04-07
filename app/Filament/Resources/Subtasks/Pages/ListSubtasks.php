<?php

namespace App\Filament\Resources\Subtasks\Pages;

use App\Filament\Resources\Subtasks\SubtaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubtasks extends ListRecords
{
    protected static string $resource = SubtaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
