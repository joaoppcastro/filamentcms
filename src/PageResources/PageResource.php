<?php

namespace JoaoCastro\FilamentCms\PageResources;

use JoaoCastro\FilamentCms\PageResources\Pages;
use JoaoCastro\FilamentCms\Models\Page;
use App\Models\Channel;
use App\Rules\UniqueSlug;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    use Translatable;

    protected static ?string $model = Page::class;

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('General information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->hint('Translatable')
                                    ->hintColor('primary')
                                    ->hintIcon('heroicon-m-language')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, $state, Set $set, Get $get, ?string $old, $livewire) {

                                        // Get Channel Domain
                                        $domain = Channel::all()->where('id', Filament::getTenant()->getAttribute('id'))->value('domain');

                                        // Get Active Language
                                        $languagePrefix = strtolower($livewire->activeLocale);

                                        // Generate new Slug
                                        $slug = $domain.'/'.$languagePrefix.'/'.Str::slug($state);

                                        // Validate if slug changed
                                        $oldSlug = $domain.'/'.$languagePrefix.'/'.Str::slug($old);
                                        if (($get('slug') ?? '') === Str::slug($oldSlug)) {
                                            return;
                                        }

                                        // Set New or Change Slug
                                        $set('slug', $slug);
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->hint('Translatable')
                                    ->hintColor('primary')
                                    ->hintIcon('heroicon-m-language')
                                    //->disabled()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->rules([new UniqueSlug($form->getRecord()?->id, 'page')])
                                    ->afterStateUpdated(function ($state, Set $set, ?string $old) {
                                        $slug = Str::slug(substr($state, strrpos("/$state", '/')));
                                        $oldSlug = substr($old, strrpos("/$old", '/'));
                                        $set('slug', str_replace($oldSlug, $slug, $old));
                                    })
                                    ->maxLength(255),

                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('Is Active'))
                                    ->required(),
                            ]),

                        Tabs\Tab::make('Content')
                            ->schema([
                                TiptapEditor::make('content')
                                    ->profile('default')
                                    ->maxContentWidth('12xl')
                                    ->output(TiptapOutput::Html)
                                    ->extraInputAttributes(['style' => 'min-height: 24rem;'])
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Meta Information')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->required(),
                                Forms\Components\Textarea::make('meta_description')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Settings')
                            ->schema([
                                TagsInput::make('tags')
                                    ->separator(',')
                                    ->suggestions([
                                        'tailwindcss',
                                        'alpinejs',
                                        'laravel',
                                        'livewire',
                                    ]),
                                DatePicker::make('published_at'),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
