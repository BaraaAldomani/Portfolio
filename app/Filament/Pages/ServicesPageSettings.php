<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ServicesPageSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    protected static ?string $navigationLabel = 'Services page';

    protected static ?string $title = 'Services page';

    protected static ?int $navigationSort = 7;

    public static function group(): string
    {
        return 'services_page';
    }

    public static function defaultValues(): array
    {
        return [
            'eyebrow_ar' => trans('services.eyebrow', [], 'ar'),
            'eyebrow_en' => trans('services.eyebrow', [], 'en'),
            'title_ar' => trans('services.title', [], 'ar'),
            'title_en' => trans('services.title', [], 'en'),
            'lead_ar' => trans('services.lead', [], 'ar'),
            'lead_en' => trans('services.lead', [], 'en'),
            'badge_ar' => trans('services.badge', [], 'ar'),
            'badge_en' => trans('services.badge', [], 'en'),
            'offer_eyebrow_ar' => trans('services.offer.eyebrow', [], 'ar'),
            'offer_eyebrow_en' => trans('services.offer.eyebrow', [], 'en'),
            'offer_title_ar' => trans('services.offer.title', [], 'ar'),
            'offer_title_en' => trans('services.offer.title', [], 'en'),
            'offer_lead_ar' => trans('services.offer.lead', [], 'ar'),
            'offer_lead_en' => trans('services.offer.lead', [], 'en'),
            'discuss_ar' => trans('services.discuss', [], 'ar'),
            'discuss_en' => trans('services.discuss', [], 'en'),
            'stats_eyebrow_ar' => trans('services.stats.eyebrow', [], 'ar'),
            'stats_eyebrow_en' => trans('services.stats.eyebrow', [], 'en'),
            'stats_title_ar' => trans('services.stats.title', [], 'ar'),
            'stats_title_en' => trans('services.stats.title', [], 'en'),
            'process_eyebrow_ar' => trans('services.process.eyebrow', [], 'ar'),
            'process_eyebrow_en' => trans('services.process.eyebrow', [], 'en'),
            'process_title_ar' => trans('services.process.title', [], 'ar'),
            'process_title_en' => trans('services.process.title', [], 'en'),
            'tech_title_ar' => trans('services.tech_title', [], 'ar'),
            'tech_title_en' => trans('services.tech_title', [], 'en'),
            'cta_title_ar' => trans('services.cta.title', [], 'ar'),
            'cta_title_en' => trans('services.cta.title', [], 'en'),
            'cta_lead_ar' => trans('services.cta.lead', [], 'ar'),
            'cta_lead_en' => trans('services.cta.lead', [], 'en'),
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Header')
                ->columns(2)
                ->schema([
                    TextInput::make('eyebrow_ar')->label('Eyebrow (Arabic)'),
                    TextInput::make('eyebrow_en')->label('Eyebrow (English)'),
                    TextInput::make('title_ar')->label('Title (Arabic)'),
                    TextInput::make('title_en')->label('Title (English)'),
                    Textarea::make('lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('lead_en')->label('Lead (English)')->rows(2),
                    TextInput::make('badge_ar')->label('Badge (Arabic)'),
                    TextInput::make('badge_en')->label('Badge (English)'),
                ]),
            Section::make('Offer')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextInput::make('offer_eyebrow_ar')->label('Eyebrow (Arabic)'),
                    TextInput::make('offer_eyebrow_en')->label('Eyebrow (English)'),
                    TextInput::make('offer_title_ar')->label('Title (Arabic)'),
                    TextInput::make('offer_title_en')->label('Title (English)'),
                    Textarea::make('offer_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('offer_lead_en')->label('Lead (English)')->rows(2),
                    TextInput::make('discuss_ar')->label('"Discuss" button (Arabic)'),
                    TextInput::make('discuss_en')->label('"Discuss" button (English)'),
                ]),
            Section::make('Section headings')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextInput::make('stats_eyebrow_ar')->label('Stats eyebrow (Arabic)'),
                    TextInput::make('stats_eyebrow_en')->label('Stats eyebrow (English)'),
                    TextInput::make('stats_title_ar')->label('Stats title (Arabic)'),
                    TextInput::make('stats_title_en')->label('Stats title (English)'),
                    TextInput::make('process_eyebrow_ar')->label('Process eyebrow (Arabic)'),
                    TextInput::make('process_eyebrow_en')->label('Process eyebrow (English)'),
                    TextInput::make('process_title_ar')->label('Process title (Arabic)'),
                    TextInput::make('process_title_en')->label('Process title (English)'),
                    TextInput::make('tech_title_ar')->label('Tech title (Arabic)'),
                    TextInput::make('tech_title_en')->label('Tech title (English)'),
                ]),
            Section::make('Call to action')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextInput::make('cta_title_ar')->label('Title (Arabic)'),
                    TextInput::make('cta_title_en')->label('Title (English)'),
                    Textarea::make('cta_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('cta_lead_en')->label('Lead (English)')->rows(2),
                ]),
        ];
    }
}
