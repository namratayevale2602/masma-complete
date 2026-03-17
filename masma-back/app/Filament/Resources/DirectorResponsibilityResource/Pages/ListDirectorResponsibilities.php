<?php

namespace App\Filament\Resources\DirectorResponsibilityResource\Pages;

use App\Filament\Resources\DirectorResponsibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDirectorResponsibilities extends ListRecords
{
    protected static string $resource = DirectorResponsibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
