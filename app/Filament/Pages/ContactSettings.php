<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ContactSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $navigationLabel = 'Contact & social';

    protected static ?string $title = 'Contact & social';

    protected static ?int $navigationSort = 5;

    public static function group(): string
    {
        return 'contact';
    }

    public static function defaultValues(): array
    {
        return [
            'email' => config('portfolio.email'),
            'whatsapp' => config('portfolio.whatsapp'),
            'phone_display' => config('portfolio.phone_display'),
            'linkedin' => config('portfolio.linkedin'),
            'gitlab' => config('portfolio.gitlab'),
            'github' => config('portfolio.github') ?: null,
            'twitter' => config('portfolio.twitter') ?: null,
            'stackoverflow' => config('portfolio.stackoverflow') ?: null,
            'youtube' => config('portfolio.youtube') ?: null,
            'cv_path' => config('portfolio.cv_path'),
            'experience_years' => config('portfolio.experience_years'),
            'cta_title_ar' => trans('v3.cta.title', [], 'ar'),
            'cta_title_en' => trans('v3.cta.title', [], 'en'),
            'cta_lead_ar' => trans('v3.cta.lead', [], 'ar'),
            'cta_lead_en' => trans('v3.cta.lead', [], 'en'),
            'cta_primary_ar' => trans('v3.cta.primary', [], 'ar'),
            'cta_primary_en' => trans('v3.cta.primary', [], 'en'),
            'page_eyebrow_ar' => trans('contact.eyebrow', [], 'ar'),
            'page_eyebrow_en' => trans('contact.eyebrow', [], 'en'),
            'page_title_ar' => trans('contact.title', [], 'ar'),
            'page_title_en' => trans('contact.title', [], 'en'),
            'page_lead_ar' => trans('contact.lead', [], 'ar'),
            'page_lead_en' => trans('contact.lead', [], 'en'),
            'whatsapp_title_ar' => trans('contact.whatsapp_title', [], 'ar'),
            'whatsapp_title_en' => trans('contact.whatsapp_title', [], 'en'),
            'whatsapp_text_ar' => trans('contact.whatsapp_text', [], 'ar'),
            'whatsapp_text_en' => trans('contact.whatsapp_text', [], 'en'),
            'email_title_ar' => trans('contact.email_title', [], 'ar'),
            'email_title_en' => trans('contact.email_title', [], 'en'),
            'email_text_ar' => trans('contact.email_text', [], 'ar'),
            'email_text_en' => trans('contact.email_text', [], 'en'),
            'response_note_ar' => trans('contact.response_note', [], 'ar'),
            'response_note_en' => trans('contact.response_note', [], 'en'),
        ];
    }

    protected function formSchema(): array
    {
        return [
            Section::make('Contact channels')
                ->columns(2)
                ->schema([
                    TextInput::make('email')->email()->required(),
                    TextInput::make('whatsapp')
                        ->label('WhatsApp number')
                        ->helperText('Digits only, international format, e.g. 966553785576.'),
                    TextInput::make('phone_display')->label('Phone (display)'),
                    TextInput::make('cv_path')->label('CV path')->helperText('Path under public/, e.g. cv/baraa-aldomani-cv.pdf.'),
                ]),
            Section::make('Social & profile')
                ->columns(2)
                ->schema([
                    TextInput::make('linkedin')->label('LinkedIn URL')->url(),
                    TextInput::make('gitlab')->label('GitLab URL')->url(),
                    TextInput::make('github')->label('GitHub URL')->url()
                        ->helperText('Boosts your identity graph for Google & AI assistants.'),
                    TextInput::make('twitter')->label('X / Twitter URL')->url(),
                    TextInput::make('stackoverflow')->label('Stack Overflow URL')->url(),
                    TextInput::make('youtube')->label('YouTube URL')->url(),
                    TextInput::make('experience_years')
                        ->label('Years of experience')
                        ->numeric()
                        ->helperText('Used in schema markup and copy.'),
                ]),
            Section::make('Global call to action')
                ->description('Shown in the header button and the footer band on every page.')
                ->columns(2)
                ->schema([
                    TextInput::make('cta_title_ar')->label('Title (Arabic)'),
                    TextInput::make('cta_title_en')->label('Title (English)'),
                    Textarea::make('cta_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('cta_lead_en')->label('Lead (English)')->rows(2),
                    TextInput::make('cta_primary_ar')->label('Button (Arabic)'),
                    TextInput::make('cta_primary_en')->label('Button (English)'),
                ]),
            Section::make('Contact page copy')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextInput::make('page_eyebrow_ar')->label('Eyebrow (Arabic)'),
                    TextInput::make('page_eyebrow_en')->label('Eyebrow (English)'),
                    TextInput::make('page_title_ar')->label('Title (Arabic)'),
                    TextInput::make('page_title_en')->label('Title (English)'),
                    Textarea::make('page_lead_ar')->label('Lead (Arabic)')->rows(2),
                    Textarea::make('page_lead_en')->label('Lead (English)')->rows(2),
                    TextInput::make('whatsapp_title_ar')->label('WhatsApp card title (Arabic)'),
                    TextInput::make('whatsapp_title_en')->label('WhatsApp card title (English)'),
                    TextInput::make('whatsapp_text_ar')->label('WhatsApp card text (Arabic)'),
                    TextInput::make('whatsapp_text_en')->label('WhatsApp card text (English)'),
                    TextInput::make('email_title_ar')->label('Email card title (Arabic)'),
                    TextInput::make('email_title_en')->label('Email card title (English)'),
                    TextInput::make('email_text_ar')->label('Email card text (Arabic)'),
                    TextInput::make('email_text_en')->label('Email card text (English)'),
                    TextInput::make('response_note_ar')->label('Response note (Arabic)'),
                    TextInput::make('response_note_en')->label('Response note (English)'),
                ]),
        ];
    }
}
