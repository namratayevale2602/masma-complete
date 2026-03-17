<?php

namespace App\Filament\Resources\EthicalStandardResource\Pages;

use App\Filament\Resources\EthicalStandardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEthicalStandard extends EditRecord
{
    protected static string $resource = EthicalStandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
