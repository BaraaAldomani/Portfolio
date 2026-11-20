<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Card & ordering')
                    ->columns(3)
                    ->schema([
                        Toggle::make('featured')
                            ->helperText('Show in the home page work showcase.'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower shows first.'),
                        Select::make('kind')
                            ->options([
                                'gov' => 'Government (no screenshots)',
                                'product' => 'Own product',
                                'client' => 'Client project',
                                'lab' => 'Earlier work (listed compactly)',
                            ])
                            ->default('client')
                            ->required()
                            ->native(false),
                        Select::make('scene')
                            ->label('Home-page world')
                            ->options(['sajjah' => 'Sajjah (built in tiles)', 'shattib' => 'Shattib (blueprint to site)', 'rouaa' => 'Rouaa (three tinted phones)', 'nour' => 'Nour (lit by a lamp)'])
                            ->placeholder('None: listed with the other work')
                            ->native(false)
                            ->helperText('Each world is a hand-built section; new ones are added in code.'),
                        TextInput::make('year')
                            ->maxLength(20)
                            ->helperText('e.g. 2026 or 2024 - 2026'),
                        TextInput::make('live_url')
                            ->label('Live site URL')
                            ->url()
                            ->columnSpan(2)
                            ->helperText('Shown as a "Visit the site" button.'),
                        FileUpload::make('image_path')
                            ->label('Desktop screenshot')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('images/projects')
                            ->visibility('public')
                            ->helperText('16:10, about 1600px wide. Leave empty to show the headline fact as dot-matrix type instead.')
                            ->columnSpan(2),
                        FileUpload::make('mobile_image_path')
                            ->label('Phone screenshot')
                            ->image()
                            ->disk('public')
                            ->directory('images/projects')
                            ->visibility('public')
                            ->helperText('Optional. Overlaps the corner of the desktop shot.'),
                    ]),

                Tabs::make('Content')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('العربية')->schema(self::localeFields('ar')),
                        Tab::make('English')->schema(self::localeFields('en')),
                    ]),
            ]);
    }

    /**
     * The bilingual field set, rendered once per locale tab.
     *
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function localeFields(string $locale): array
    {
        return [
            TextInput::make("slug_{$locale}")
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('URL segment, e.g. my-project.'),
            TextInput::make("name_{$locale}")
                ->label('Short name')
                ->maxLength(80)
                ->helperText('The big display name, e.g. "Sajjah". Falls back to the title.'),
            TextInput::make("title_{$locale}")
                ->label('Title')
                ->required()
                ->maxLength(255),
            TextInput::make("sector_{$locale}")
                ->label('Sector')
                ->required()
                ->maxLength(120)
                ->helperText('e.g. Government · Housing'),
            Textarea::make("summary_{$locale}")
                ->label('Summary')
                ->required()
                ->maxLength(500)
                ->rows(2),
            TextInput::make("role_{$locale}")
                ->label('What I built')
                ->maxLength(200)
                ->helperText('e.g. The website, booking flow and dashboards.'),
            TextInput::make("stat_{$locale}")
                ->label('Headline fact')
                ->maxLength(120)
                ->helperText('One short line. Used as the big dot-matrix text when there is no screenshot.'),
            Textarea::make("problem_{$locale}")
                ->label('Problem')
                ->required()
                ->rows(3),
            Textarea::make("solution_{$locale}")
                ->label('Solution')
                ->required()
                ->rows(3),
            Textarea::make("result_{$locale}")
                ->label('Result')
                ->required()
                ->rows(3),
            TagsInput::make("highlights_{$locale}")
                ->label('Highlights')
                ->helperText('Press Enter after each. Client-facing capabilities, not a tech stack.'),
        ];
    }
}
