<?php

namespace App\Filament\Resources\Subtasks\Pages;

use App\Filament\Resources\Subtasks\SubtaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubtask extends EditRecord
{
    protected static string $resource = SubtaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
