<?php

namespace App\Filament\Resources\EthicalStandardResource\Pages;

use App\Filament\Resources\EthicalStandardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEthicalStandards extends ListRecords
{
    protected static string $resource = EthicalStandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
