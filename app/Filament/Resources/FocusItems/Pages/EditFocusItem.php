<?php

namespace App\Filament\Resources\FocusItems\Pages;

use App\Filament\Resources\FocusItems\FocusItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFocusItem extends EditRecord
{
    protected static string $resource = FocusItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
