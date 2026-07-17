<?php

namespace App\Filament\Resources\Technologies;

use App\Filament\Resources\Technologies\Pages\CreateTechnology;
use App\Filament\Resources\Technologies\Pages\EditTechnology;
use App\Filament\Resources\Technologies\Pages\ListTechnologies;
use App\Filament\Resources\Technologies\Schemas\TechnologyForm;
use App\Filament\Resources\Technologies\Tables\TechnologiesTable;
use App\Models\Technology;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Tech stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TechnologyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TechnologiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTechnologies::route('/'),
            'create' => CreateTechnology::route('/create'),
            'edit' => EditTechnology::route('/{record}/edit'),
        ];
    }
}
