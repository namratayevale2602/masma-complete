<?php

namespace App\Filament\Resources\RegionalDirectorResource\Pages;

use App\Filament\Resources\RegionalDirectorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegionalDirector extends EditRecord
{
    protected static string $resource = RegionalDirectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
