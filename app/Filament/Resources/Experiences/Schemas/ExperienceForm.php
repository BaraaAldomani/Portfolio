<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('role_ar')->label('Role (Arabic)')->required(),
                        TextInput::make('role_en')->label('Role (English)')->required(),
                        TextInput::make('org_ar')->label('Organisation (Arabic)')->required(),
                        TextInput::make('org_en')->label('Organisation (English)')->required(),
                        TextInput::make('period_ar')->label('Period (Arabic)')->required()->maxLength(60),
                        TextInput::make('period_en')->label('Period (English)')->required()->maxLength(60),
                        Textarea::make('blurb_ar')->label('Blurb (Arabic)')->required()->rows(3),
                        Textarea::make('blurb_en')->label('Blurb (English)')->required()->rows(3),
                        TextInput::make('url')->label('Link')->url()->columnSpanFull(),
                        TextInput::make('sort_order')->numeric()->default(0),
                    ]),
            ]);
    }
}
