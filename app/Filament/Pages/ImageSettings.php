<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ImageSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Images';

    protected static ?string $title = 'Images';

    protected static ?int $navigationSort = 8;

    public static function group(): string
    {
        return 'images';
    }

    public static function defaultValues(): array
    {
        // Uploads start empty; the public site falls back to the built-in
        // config('portfolio.images.*') asset until a file is uploaded here.
        return [
            'portrait' => null,
            'about_portrait' => null,
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('People')
                ->description('Project screenshots are uploaded on each project (Projects).')
                ->columns(2)
                ->schema([
                    $this->upload('about_portrait', 'About page portrait'),
                    $this->upload('portrait', 'Portrait for search engines'),
                ]),
        ];
    }

    private function upload(string $key, string $label): FileUpload
    {
        return FileUpload::make($key)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory('images/uploads')
            ->visibility('public')
            ->helperText('Leave empty to use the built-in default.');
    }
}
