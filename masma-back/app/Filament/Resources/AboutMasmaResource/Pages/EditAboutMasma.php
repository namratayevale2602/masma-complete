<?php

namespace App\Filament\Resources\AboutMasmaResource\Pages;

use App\Filament\Resources\AboutMasmaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutMasma extends EditRecord
{
    protected static string $resource = AboutMasmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
