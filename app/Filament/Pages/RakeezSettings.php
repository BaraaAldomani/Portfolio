<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class RakeezSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Rakeez block';

    protected static ?string $title = 'Rakeez block';

    protected static ?int $navigationSort = 4;

    public static function group(): string
    {
        return 'rakeez';
    }

    public static function defaultValues(): array
    {
        return [
            'eyebrow_ar' => trans('v3.rakeez.eyebrow', [], 'ar'),
            'eyebrow_en' => trans('v3.rakeez.eyebrow', [], 'en'),
            'brand_ar' => trans('v3.rakeez.title_ar', [], 'ar'),
            'brand_en' => trans('v3.rakeez.title', [], 'en'),
            'lead_ar' => trans('v3.rakeez.lead', [], 'ar'),
            'lead_en' => trans('v3.rakeez.lead', [], 'en'),
            'points_ar' => (array) trans('v3.rakeez.points', [], 'ar'),
            'points_en' => (array) trans('v3.rakeez.points', [], 'en'),
            'cta_ar' => trans('v3.rakeez.cta', [], 'ar'),
            'cta_en' => trans('v3.rakeez.cta', [], 'en'),
            'url' => trans('v3.rakeez.url', [], 'en'),
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Rakeez founder block')
                ->columns(2)
                ->schema([
                    TextInput::make('eyebrow_ar')->label('Eyebrow (Arabic)'),
                    TextInput::make('eyebrow_en')->label('Eyebrow (English)'),
                    TextInput::make('brand_ar')->label('Brand name (Arabic)'),
                    TextInput::make('brand_en')->label('Brand name (English)'),
                    Textarea::make('lead_ar')->label('Lead (Arabic)')->rows(3),
                    Textarea::make('lead_en')->label('Lead (English)')->rows(3),
                    TagsInput::make('points_ar')->label('Points (Arabic)'),
                    TagsInput::make('points_en')->label('Points (English)'),
                    TextInput::make('cta_ar')->label('Button (Arabic)'),
                    TextInput::make('cta_en')->label('Button (English)'),
                    TextInput::make('url')->label('Link')->url()->columnSpanFull(),
                ]),
        ];
    }
}
