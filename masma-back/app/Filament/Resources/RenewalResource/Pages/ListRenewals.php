<?php

namespace App\Filament\Resources\RenewalResource\Pages;

use App\Filament\Resources\RenewalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRenewals extends ListRecords
{
    protected static string $resource = RenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
