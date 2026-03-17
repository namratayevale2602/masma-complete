<?php

namespace App\Filament\Resources\DirectorResponsibilityResource\Pages;

use App\Filament\Resources\DirectorResponsibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDirectorResponsibility extends EditRecord
{
    protected static string $resource = DirectorResponsibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
