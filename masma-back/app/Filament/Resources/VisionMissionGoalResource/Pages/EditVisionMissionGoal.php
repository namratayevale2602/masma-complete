<?php

namespace App\Filament\Resources\VisionMissionGoalResource\Pages;

use App\Filament\Resources\VisionMissionGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisionMissionGoal extends EditRecord
{
    protected static string $resource = VisionMissionGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
