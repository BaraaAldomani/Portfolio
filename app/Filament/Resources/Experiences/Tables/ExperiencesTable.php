<?php

namespace App\Filament\Resources\Experiences\Tables;

use App\Models\Experience;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('role_en')
                    ->label('Role')
                    ->description(fn (Experience $record): string => $record->role_ar)
                    ->searchable(),
                TextColumn::make('org_en')
                    ->label('Organisation')
                    ->description(fn (Experience $record): string => $record->org_ar)
                    ->searchable(),
                TextColumn::make('period_en')->label('Period')->toggleable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
