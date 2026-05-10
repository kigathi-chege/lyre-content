<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Facet\Filament\RelationManagers\FacetValuesRelationManager;
use Lyre\File\Filament\RelationManagers\FilesRelationManager;
use Lyre\Content\Filament\Resources\ArticleResource\Pages;
use Lyre\Content\Filament\Actions\FormatArticleWithAIAction;
use Lyre\Content\Filament\Actions\FormatSingleArticleWithAIAction;
use Lyre\Content\Models\Article;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Lyre\File\Filament\Forms\Components\SelectFromGallery;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static \BackedEnum|string|null $navigationIcon = 'gmdi-newspaper';

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'Content';
    }

    protected static ?string $navigationLabel = 'Blog';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('subtitle')
                    ->maxLength(255)
                    ->columnSpanFull(),
                SelectFromGallery::make('files')->label('Featured Image'),
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->preload()
                    ->searchable(),
                TiptapEditor::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'height: 500px',
                    ]),
                Forms\Components\Toggle::make('is_featured')
                    ->required(),
                Forms\Components\Toggle::make('unpublished')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\DateTimePicker::make('sent_as_newsletter_at'),
                TiptapEditor::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('categories')
                    ->label('Categories')
                    ->options(function () {
                        return \Lyre\Facet\Models\FacetValue::query()
                            ->with('facet')
                            ->get()
                            ->mapWithKeys(function ($facetValue) {
                                $label = $facetValue->facet
                                    ? "{$facetValue->name} ({$facetValue->facet->name})"
                                    : $facetValue->name;

                                return [$facetValue->id => $label];
                            })
                            ->toArray();
                    })
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if (! $record) {
                            return;
                        }

                        $prefix = config('lyre.table_prefix');

                        $component->state(
                            $record->facetValues()
                                ->pluck("{$prefix}facet_values.id")
                                ->toArray()
                        );
                    })
                    ->saveRelationshipsUsing(function ($component, $record, $state) {
                        if (! empty($state)) {
                            $record->attachFacetValues($state);
                        }
                    })
                    ->dehydrated(false)
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Select::make('facet_id')
                            ->relationship('facet', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_ai_formatted')
                    ->label('AI Formatted')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'published',
                        'danger' => 'unpublished',
                    ]),
                Tables\Columns\TextColumn::make('author.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                FormatSingleArticleWithAIAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    FormatArticleWithAIAction::make(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([])
            ->striped()
            ->deferLoading()
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            FacetValuesRelationManager::class,
            FilesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // $permissions = config('filament-shield.permission_prefixes.resource');
        // TODO: Kigathi - May 4 2025 - Users should only view this navigation if they have at least one more permission than view and viewAny
        $usingSpatieRoles = in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses(\App\Models\User::class));
        return $usingSpatieRoles ? Auth::user()->can('update', new Article) : true;
    }
}
