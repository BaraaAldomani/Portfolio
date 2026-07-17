<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class AboutSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'About page';

    protected static ?string $title = 'About page';

    protected static ?int $navigationSort = 3;

    public static function group(): string
    {
        return 'about';
    }

    public static function defaultValues(): array
    {
        return [
            'eyebrow_ar' => trans('about.eyebrow', [], 'ar'),
            'eyebrow_en' => trans('about.eyebrow', [], 'en'),
            'title_ar' => trans('about.title', [], 'ar'),
            'title_en' => trans('about.title', [], 'en'),
            'lead_ar' => trans('about.lead', [], 'ar'),
            'lead_en' => trans('about.lead', [], 'en'),
            'work_title_ar' => trans('v3.work.title', [], 'ar'),
            'work_title_en' => trans('v3.work.title', [], 'en'),
            'work_lead_ar' => trans('v3.work.lead', [], 'ar'),
            'work_lead_en' => trans('v3.work.lead', [], 'en'),
            'bio_ar' => implode("\n\n", (array) trans('about.bio', [], 'ar')),
            'bio_en' => implode("\n\n", (array) trans('about.bio', [], 'en')),
            'capabilities_title_ar' => trans('about.capabilities.title', [], 'ar'),
            'capabilities_title_en' => trans('about.capabilities.title', [], 'en'),
            'education_title_ar' => trans('about.education.title', [], 'ar'),
            'education_title_en' => trans('about.education.title', [], 'en'),
            'education_degree_ar' => trans('about.education.degree', [], 'ar'),
            'education_degree_en' => trans('about.education.degree', [], 'en'),
            'education_school_ar' => trans('about.education.school', [], 'ar'),
            'education_school_en' => trans('about.education.school', [], 'en'),
            'education_year_ar' => trans('about.education.year', [], 'ar'),
            'education_year_en' => trans('about.education.year', [], 'en'),
            'education_languages_ar' => trans('about.education.languages', [], 'ar'),
            'education_languages_en' => trans('about.education.languages', [], 'en'),
            'cta_title_ar' => trans('about.cta.title', [], 'ar'),
            'cta_title_en' => trans('about.cta.title', [], 'en'),
            'cta_lead_ar' => trans('about.cta.lead', [], 'ar'),
            'cta_lead_en' => trans('about.cta.lead', [], 'en'),
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Intro')
                ->columns(2)
                ->schema([
                    TextInput::make('eyebrow_ar')->label('Eyebrow (Arabic)'),
                    TextInput::make('eyebrow_en')->label('Eyebrow (English)'),
                    TextInput::make('title_ar')->label('Title (Arabic)'),
                    TextInput::make('title_en')->label('Title (English)'),
                    Textarea::make('lead_ar')->label('Lead (Arabic)')->rows(3),
                    Textarea::make('lead_en')->label('Lead (English)')->rows(3),
                    Textarea::make('bio_ar')->label('Bio (Arabic)')->rows(5)
                        ->helperText('Separate paragraphs with a blank line.'),
                    Textarea::make('bio_en')->label('Bio (English)')->rows(5)
                        ->helperText('Separate paragraphs with a blank line.'),
                ]),
            Section::make('Section headings')
                ->columns(2)
                ->schema([
                    TextInput::make('work_title_ar')->label('Work heading (Arabic)'),
                    TextInput::make('work_title_en')->label('Work heading (English)'),
                    Textarea::make('work_lead_ar')->label('Work lead (Arabic)')->rows(2),
                    Textarea::make('work_lead_en')->label('Work lead (English)')->rows(2),
                    TextInput::make('capabilities_title_ar')->label('Capabilities title (Arabic)'),
                    TextInput::make('capabilities_title_en')->label('Capabilities title (English)'),
                ]),
            Section::make('Education')
                ->columns(2)
                ->schema([
                    TextInput::make('education_title_ar')->label('Heading (Arabic)'),
                    TextInput::make('education_title_en')->label('Heading (English)'),
                    TextInput::make('education_degree_ar')->label('Degree (Arabic)'),
                    TextInput::make('education_degree_en')->label('Degree (English)'),
                    TextInput::make('education_school_ar')->label('School (Arabic)'),
                    TextInput::make('education_school_en')->label('School (English)'),
                    TextInput::make('education_year_ar')->label('Year (Arabic)'),
                    TextInput::make('education_year_en')->label('Year (English)'),
                    TextInput::make('education_languages_ar')->label('Languages (Arabic)'),
                    TextInput::make('education_languages_en')->label('Languages (English)'),
                ]),
            Section::make('Call to action')
                ->columns(2)
                ->schema([
                    TextInput::make('cta_title_ar')->label('Title (Arabic)'),
                    TextInput::make('cta_title_en')->label('Title (English)'),
                    Textarea::make('cta_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('cta_lead_en')->label('Lead (English)')->rows(2),
                ]),
        ];
    }
}
