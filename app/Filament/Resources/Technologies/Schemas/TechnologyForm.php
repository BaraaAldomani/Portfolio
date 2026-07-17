<?php

namespace App\Filament\Resources\Technologies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TechnologyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('sort_order')->numeric()->default(0),
                    ]),
            ]);
    }
}
