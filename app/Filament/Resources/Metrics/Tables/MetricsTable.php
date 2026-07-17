<?php

namespace App\Filament\Resources\Metrics\Tables;

use App\Models\Metric;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MetricsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('label_en')
                    ->label('Label')
                    ->description(fn (Metric $record): string => $record->label_ar)
                    ->searchable(),
                TextColumn::make('value')
                    ->formatStateUsing(fn (Metric $record): string => $record->value . $record->suffix),
                TextColumn::make('context')->badge()->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
