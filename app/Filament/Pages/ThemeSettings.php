<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ThemeSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Theme';

    protected static ?string $title = 'Theme';

    protected static ?int $navigationSort = 1;

    public static function group(): string
    {
        return 'theme';
    }

    public static function defaultValues(): array
    {
        return [
            'primary' => '#4f46e5',
            'secondary' => '#0f172a',
            'accent' => '#14b8a6',
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Brand colours')
                ->description('These three colours drive the whole site. Every shade, surface, border, glow and gradient is derived from them automatically, so a change here re-themes every page.')
                ->columns(3)
                ->schema([
                    ColorPicker::make('primary')
                        ->hex()
                        ->required()
                        ->helperText('Brand, links, buttons.'),
                    ColorPicker::make('secondary')
                        ->hex()
                        ->required()
                        ->helperText('Dark sections, text.'),
                    ColorPicker::make('accent')
                        ->hex()
                        ->required()
                        ->helperText('Highlights, glow, terminal.'),
                ]),
        ];
    }
}
