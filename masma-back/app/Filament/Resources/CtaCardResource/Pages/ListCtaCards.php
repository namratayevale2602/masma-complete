<?php

namespace App\Filament\Resources\CtaCardResource\Pages;

use App\Filament\Resources\CtaCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCtaCards extends ListRecords
{
    protected static string $resource = CtaCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
