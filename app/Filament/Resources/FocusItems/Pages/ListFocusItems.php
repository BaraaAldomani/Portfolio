<?php

namespace App\Filament\Resources\FocusItems\Pages;

use App\Filament\Resources\FocusItems\FocusItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFocusItems extends ListRecords
{
    protected static string $resource = FocusItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
