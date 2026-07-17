<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SeoSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'SEO';

    protected static ?string $title = 'SEO';

    protected static ?int $navigationSort = 6;

    private const PAGES = ['home', 'services', 'projects', 'about', 'contact', 'blog'];

    public static function group(): string
    {
        return 'seo';
    }

    public static function defaultValues(): array
    {
        $defaults = [];

        foreach (self::PAGES as $page) {
            $defaults["{$page}_title_ar"] = trans("seo.{$page}.title", [], 'ar');
            $defaults["{$page}_title_en"] = trans("seo.{$page}.title", [], 'en');
            $defaults["{$page}_description_ar"] = trans("seo.{$page}.description", [], 'ar');
            $defaults["{$page}_description_en"] = trans("seo.{$page}.description", [], 'en');
        }

        return $defaults;
    }

    protected function formSchema(): array
    {
        return array_map(
            fn (string $page): Section => Section::make(ucfirst($page))
                ->columns(2)
                ->collapsible()
                ->collapsed($page !== 'home')
                ->schema([
                    TextInput::make("{$page}_title_ar")->label('Title (Arabic)'),
                    TextInput::make("{$page}_title_en")->label('Title (English)'),
                    Textarea::make("{$page}_description_ar")->label('Description (Arabic)')->rows(2),
                    Textarea::make("{$page}_description_en")->label('Description (English)')->rows(2),
                ]),
            self::PAGES,
        );
    }
}
