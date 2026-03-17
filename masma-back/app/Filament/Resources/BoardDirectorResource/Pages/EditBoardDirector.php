<?php

namespace App\Filament\Resources\BoardDirectorResource\Pages;

use App\Filament\Resources\BoardDirectorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoardDirector extends EditRecord
{
    protected static string $resource = BoardDirectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
