<?php

namespace App\Filament\Resources\ProcessSteps\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProcessStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_ar')->label('Title (Arabic)')->required(),
                        TextInput::make('title_en')->label('Title (English)')->required(),
                        Textarea::make('blurb_ar')->label('Blurb (Arabic)')->required()->rows(3),
                        Textarea::make('blurb_en')->label('Blurb (English)')->required()->rows(3),
                        TextInput::make('sort_order')->numeric()->default(0)->columnSpanFull(),
                    ]),
            ]);
    }
}
