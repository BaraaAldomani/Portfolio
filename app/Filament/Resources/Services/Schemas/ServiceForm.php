<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setup')
                    ->columns(2)
                    ->schema([
                        TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Stable identifier, e.g. systems.'),
                        Select::make('icon_key')
                            ->label('Icon')
                            ->options([
                                'systems' => 'Systems',
                                'web_apps' => 'Web & mobile',
                                'websites' => 'Websites',
                                'apis' => 'APIs',
                            ])
                            ->native(false),
                        TextInput::make('sort_order')->numeric()->default(0),
                        FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('images/services')
                            ->visibility('public')
                            ->helperText('Optional. Falls back to the built-in illustration.'),
                    ]),
                Section::make('Content')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_ar')->label('Title (Arabic)')->required(),
                        TextInput::make('title_en')->label('Title (English)')->required(),
                        Textarea::make('description_ar')->label('Description (Arabic)')->required()->rows(3),
                        Textarea::make('description_en')->label('Description (English)')->required()->rows(3),
                        TagsInput::make('points_ar')->label('Points (Arabic)'),
                        TagsInput::make('points_en')->label('Points (English)'),
                        TextInput::make('tag_ar')->label('Tag (Arabic)'),
                        TextInput::make('tag_en')->label('Tag (English)'),
                        TextInput::make('image_alt_ar')->label('Image alt (Arabic)'),
                        TextInput::make('image_alt_en')->label('Image alt (English)'),
                    ]),
            ]);
    }
}
