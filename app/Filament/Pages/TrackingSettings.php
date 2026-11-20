<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

/**
 * Analytics + search-engine ownership codes, editable without a deploy.
 * Rendered by resources/views/components/tracking-head.blade.php; analytics
 * scripts load in production only.
 */
class TrackingSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = 'Analytics & verification';

    protected static ?string $title = 'Analytics & verification';

    protected static ?int $navigationSort = 7;

    public const UMAMI_DEFAULT_SCRIPT = 'https://cloud.umami.is/script.js';

    public static function group(): string
    {
        return 'tracking';
    }

    public static function defaultValues(): array
    {
        return [
            'google_verification' => null,
            'bing_verification' => null,
            'umami_website_id' => null,
            'umami_script_url' => self::UMAMI_DEFAULT_SCRIPT,
            'ga4_id' => null,
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Search engine verification')
                ->description('Paste the code (or the whole <meta> tag) from Google Search Console and Bing Webmaster Tools, using the "HTML tag" method.')
                ->columns(2)
                ->schema([
                    TextInput::make('google_verification')->label('Google Search Console'),
                    TextInput::make('bing_verification')->label('Bing Webmaster Tools'),
                ]),
            Section::make('Analytics')
                ->description('Umami is cookie-free (recommended). GA4 works too. Clicks on WhatsApp, email and contact buttons, and sent contact forms, are tracked as events automatically.')
                ->columns(2)
                ->schema([
                    TextInput::make('umami_website_id')
                        ->label('Umami website ID')
                        ->regex('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i')
                        ->placeholder('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
                    TextInput::make('umami_script_url')
                        ->label('Umami script URL')
                        ->url()
                        ->placeholder(self::UMAMI_DEFAULT_SCRIPT),
                    TextInput::make('ga4_id')
                        ->label('Google Analytics 4 measurement ID')
                        ->regex('/^G-[A-Z0-9]+$/')
                        ->placeholder('G-XXXXXXXXXX'),
                ]),
        ];
    }
}
