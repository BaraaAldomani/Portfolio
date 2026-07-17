<?php

namespace App\Filament\Resources\Capabilities;

use App\Filament\Resources\Capabilities\Pages\CreateCapability;
use App\Filament\Resources\Capabilities\Pages\EditCapability;
use App\Filament\Resources\Capabilities\Pages\ListCapabilities;
use App\Filament\Resources\Capabilities\Schemas\CapabilityForm;
use App\Filament\Resources\Capabilities\Tables\CapabilitiesTable;
use App\Models\Capability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CapabilityResource extends Resource
{
    protected static ?string $model = Capability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'label_en';

    public static function form(Schema $schema): Schema
    {
        return CapabilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapabilitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCapabilities::route('/'),
            'create' => CreateCapability::route('/create'),
            'edit' => EditCapability::route('/{record}/edit'),
        ];
    }
}
