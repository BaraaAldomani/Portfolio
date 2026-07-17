<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class HeroSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Home page';

    protected static ?string $title = 'Home page';

    protected static ?int $navigationSort = 2;

    public static function group(): string
    {
        return 'home';
    }

    public static function defaultValues(): array
    {
        return [
            'available_ar' => trans('v3.available', [], 'ar'),
            'available_en' => trans('v3.available', [], 'en'),
            'role_ar' => trans('v3.role', [], 'ar'),
            'role_en' => trans('v3.role', [], 'en'),
            'hero_title_ar' => trans('v3.hero.title', [], 'ar'),
            'hero_title_en' => trans('v3.hero.title', [], 'en'),
            'hero_lead_ar' => trans('v3.hero.lead', [], 'ar'),
            'hero_lead_en' => trans('v3.hero.lead', [], 'en'),
            'hero_primary_ar' => trans('v3.hero.primary', [], 'ar'),
            'hero_primary_en' => trans('v3.hero.primary', [], 'en'),
            'hero_secondary_ar' => trans('v3.hero.secondary', [], 'ar'),
            'hero_secondary_en' => trans('v3.hero.secondary', [], 'en'),
            'proof_title_ar' => trans('v3.proof.title', [], 'ar'),
            'proof_title_en' => trans('v3.proof.title', [], 'en'),
            'proof_lead_ar' => trans('v3.proof.lead', [], 'ar'),
            'proof_lead_en' => trans('v3.proof.lead', [], 'en'),
            'focus_title_ar' => trans('v3.focus.title', [], 'ar'),
            'focus_title_en' => trans('v3.focus.title', [], 'en'),
            'focus_lead_ar' => trans('v3.focus.lead', [], 'ar'),
            'focus_lead_en' => trans('v3.focus.lead', [], 'en'),
            'projects_eyebrow_ar' => trans('home.projects.eyebrow', [], 'ar'),
            'projects_eyebrow_en' => trans('home.projects.eyebrow', [], 'en'),
            'projects_title_ar' => trans('home.projects.title', [], 'ar'),
            'projects_title_en' => trans('home.projects.title', [], 'en'),
            'projects_lead_ar' => trans('home.projects.lead', [], 'ar'),
            'projects_lead_en' => trans('home.projects.lead', [], 'en'),
            'detailcta_title_ar' => trans('home.cta.title', [], 'ar'),
            'detailcta_title_en' => trans('home.cta.title', [], 'en'),
            'detailcta_lead_ar' => trans('home.cta.lead', [], 'ar'),
            'detailcta_lead_en' => trans('home.cta.lead', [], 'en'),
            'detailcta_button_ar' => trans('home.cta.button', [], 'ar'),
            'detailcta_button_en' => trans('home.cta.button', [], 'en'),
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Hero')
                ->columns(2)
                ->schema([
                    TextInput::make('available_ar')->label('Availability badge (Arabic)'),
                    TextInput::make('available_en')->label('Availability badge (English)'),
                    TextInput::make('role_ar')->label('Role (Arabic)'),
                    TextInput::make('role_en')->label('Role (English)'),
                    TextInput::make('hero_title_ar')->label('Title (Arabic)'),
                    TextInput::make('hero_title_en')->label('Title (English)'),
                    Textarea::make('hero_lead_ar')->label('Lead (Arabic)')->rows(3),
                    Textarea::make('hero_lead_en')->label('Lead (English)')->rows(3),
                    TextInput::make('hero_primary_ar')->label('Primary button (Arabic)'),
                    TextInput::make('hero_primary_en')->label('Primary button (English)'),
                    TextInput::make('hero_secondary_ar')->label('Secondary button (Arabic)'),
                    TextInput::make('hero_secondary_en')->label('Secondary button (English)'),
                ]),
            Section::make('Proof')
                ->columns(2)
                ->schema([
                    TextInput::make('proof_title_ar')->label('Title (Arabic)'),
                    TextInput::make('proof_title_en')->label('Title (English)'),
                    Textarea::make('proof_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('proof_lead_en')->label('Lead (English)')->rows(2),
                ]),
            Section::make('Focus')
                ->columns(2)
                ->schema([
                    TextInput::make('focus_title_ar')->label('Title (Arabic)'),
                    TextInput::make('focus_title_en')->label('Title (English)'),
                    Textarea::make('focus_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('focus_lead_en')->label('Lead (English)')->rows(2),
                ]),
            Section::make('Projects heading')
                ->description('Shown on the home "selected work" section and the projects page.')
                ->columns(2)
                ->schema([
                    TextInput::make('projects_eyebrow_ar')->label('Eyebrow (Arabic)'),
                    TextInput::make('projects_eyebrow_en')->label('Eyebrow (English)'),
                    TextInput::make('projects_title_ar')->label('Title (Arabic)'),
                    TextInput::make('projects_title_en')->label('Title (English)'),
                    Textarea::make('projects_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('projects_lead_en')->label('Lead (English)')->rows(2),
                ]),
            Section::make('Project page call to action')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextInput::make('detailcta_title_ar')->label('Title (Arabic)'),
                    TextInput::make('detailcta_title_en')->label('Title (English)'),
                    Textarea::make('detailcta_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('detailcta_lead_en')->label('Lead (English)')->rows(2),
                    TextInput::make('detailcta_button_ar')->label('Button (Arabic)'),
                    TextInput::make('detailcta_button_en')->label('Button (English)'),
                ]),
        ];
    }
}
