<?php

namespace App\Filament\Resources\RenewalResource\Pages;

use App\Filament\Resources\RenewalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRenewal extends EditRecord
{
    protected static string $resource = RenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
