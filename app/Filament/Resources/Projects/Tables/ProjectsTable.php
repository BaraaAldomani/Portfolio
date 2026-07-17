<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('cover')
                    ->label('')
                    ->getStateUsing(fn (Project $record): string => $record->coverImage())
                    ->height(40)
                    ->extraImgAttributes(['class' => 'rounded-md object-cover']),
                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->limit(40)
                    ->description(fn (Project $record): string => $record->title_ar),
                TextColumn::make('sector_en')
                    ->label('Sector')
                    ->toggleable(),
                ToggleColumn::make('featured')
                    ->label('Featured'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
