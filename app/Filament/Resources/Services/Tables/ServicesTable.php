<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->getStateUsing(fn (Service $record): string => $record->imageUrl())
                    ->height(36),
                TextColumn::make('title_en')
                    ->label('Title')
                    ->description(fn (Service $record): string => $record->title_ar)
                    ->searchable(),
                TextColumn::make('tag_en')->label('Tag')->badge()->toggleable(),
                TextColumn::make('key')->badge()->color('gray')->toggleable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
