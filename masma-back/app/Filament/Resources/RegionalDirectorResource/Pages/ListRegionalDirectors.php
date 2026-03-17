<?php

namespace App\Filament\Resources\RegionalDirectorResource\Pages;

use App\Filament\Resources\RegionalDirectorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegionalDirectors extends ListRecords
{
    protected static string $resource = RegionalDirectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
