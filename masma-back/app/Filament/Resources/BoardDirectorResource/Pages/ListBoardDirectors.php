<?php

namespace App\Filament\Resources\BoardDirectorResource\Pages;

use App\Filament\Resources\BoardDirectorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBoardDirectors extends ListRecords
{
    protected static string $resource = BoardDirectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
