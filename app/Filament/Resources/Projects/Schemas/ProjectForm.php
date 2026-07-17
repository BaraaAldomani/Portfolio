<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
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
                            ->helperText('Show on the home page "Selected work".'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower shows first.'),
                        FileUpload::make('image_path')
                            ->label('Cover image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('images/projects')
                            ->visibility('public')
                            ->helperText('Leave empty to use the generated placeholder.')
                            ->columnSpanFull(),
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
