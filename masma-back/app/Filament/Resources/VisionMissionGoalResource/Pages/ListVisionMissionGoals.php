<?php

namespace App\Filament\Resources\VisionMissionGoalResource\Pages;

use App\Filament\Resources\VisionMissionGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisionMissionGoals extends ListRecords
{
    protected static string $resource = VisionMissionGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
