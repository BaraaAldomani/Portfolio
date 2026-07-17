<?php

namespace App\Filament\Resources\ProcessSteps\Tables;

use App\Models\ProcessStep;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProcessStepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title_en')
                    ->label('Title')
                    ->description(fn (ProcessStep $record): string => $record->title_ar)
                    ->searchable(),
                TextColumn::make('blurb_en')->label('Blurb')->limit(60)->toggleable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
