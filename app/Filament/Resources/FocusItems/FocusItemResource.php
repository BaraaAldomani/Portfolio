<?php

namespace App\Filament\Resources\FocusItems;

use App\Filament\Resources\FocusItems\Pages\CreateFocusItem;
use App\Filament\Resources\FocusItems\Pages\EditFocusItem;
use App\Filament\Resources\FocusItems\Pages\ListFocusItems;
use App\Filament\Resources\FocusItems\Schemas\FocusItemForm;
use App\Filament\Resources\FocusItems\Tables\FocusItemsTable;
use App\Models\FocusItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FocusItemResource extends Resource
{
    protected static ?string $model = FocusItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Focus areas';

    protected static ?string $recordTitleAttribute = 'title_en';

    public static function form(Schema $schema): Schema
    {
        return FocusItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FocusItemsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFocusItems::route('/'),
            'create' => CreateFocusItem::route('/create'),
            'edit' => EditFocusItem::route('/{record}/edit'),
        ];
    }
}
