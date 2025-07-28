<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\PageResource\Pages;
use Lyre\Content\Filament\Resources\PageResource\RelationManagers;
use Lyre\Facet\Filament\RelationManagers\FacetValuesRelationManager;
use Lyre\Content\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'gmdi-open-in-new';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    Forms\Components\Tabs::make('Tabs')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('Content')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('link')
                                        ->maxLength(255),
                                    TiptapEditor::make('content')
                                        ->columnSpanFull(),
                                    TiptapEditor::make('description')
                                        ->columnSpanFull(),
                                    Forms\Components\Toggle::make('is_published'),
                                ])->columns(2),
                            Forms\Components\Tabs\Tab::make('SEO')
                                ->schema([
                                    Forms\Components\Textarea::make('meta_description')
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('keywords')
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('canonical_url')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('robots_meta_tag')
                                        ->required()
                                        ->maxLength(255)
                                        ->default('index'),
                                    Forms\Components\TextInput::make('schema_markup'),
                                ]),
                            Forms\Components\Tabs\Tab::make('Open Graph')
                                ->schema([
                                    Forms\Components\TextInput::make('og_title')
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('og_description')
                                        ->columnSpanFull(),
                                    Forms\Components\FileUpload::make('og_image')
                                        ->image(),
                                ]),
                            Forms\Components\Tabs\Tab::make('Twitter')
                                ->schema([
                                    Forms\Components\TextInput::make('twitter_title')
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('twitter_description')
                                        ->columnSpanFull(),
                                    Forms\Components\FileUpload::make('twitter_image')
                                        ->image(),
                                ]),
                            Forms\Components\Tabs\Tab::make('Analytics')
                                ->schema([
                                    Forms\Components\TextInput::make('total_views')
                                        ->required()
                                        ->numeric()
                                        ->disabled()
                                        ->default(0),
                                ]),
                        ])->columnSpanFull()
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_views')
                    ->badge()
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SectionsRelationManager::class,
            FacetValuesRelationManager::class,
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

    public static function shouldRegisterNavigation(): bool
    {
        // $permissions = config('filament-shield.permission_prefixes.resource');
        // TODO: Kigathi - May 4 2025 - Users should only view this navigation if they have at least one more permission than view and viewAny
        $usingSpatieRoles = in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses(\App\Models\User::class));
        return $usingSpatieRoles ? Auth::user()->can('update', new Page) : true;
    }
}
