<?php

namespace App\Filament\Resources\Metrics\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MetricForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('context')
                            ->required()
                            ->options([
                                'home' => 'Home page',
                                'services' => 'Services page',
                            ])
                            ->default('home')
                            ->native(false),
                        TextInput::make('sort_order')->numeric()->default(0),
                        TextInput::make('value')->numeric()->required()->default(0),
                        TextInput::make('suffix')->maxLength(8)->helperText('e.g. plus or percent sign'),
                        TextInput::make('label_ar')->label('Label (Arabic)')->required(),
                        TextInput::make('label_en')->label('Label (English)')->required(),
                    ]),
            ]);
    }
}
